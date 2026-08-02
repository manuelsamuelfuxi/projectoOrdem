<?php

namespace App\Services;

use App\Models\ConfiguracaoPagamento;
use App\Models\Pedido;
use App\Models\Pagamento;
use App\Enums\EstadoPedido;
use App\Events\PagamentoConfirmado;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PagamentoService
{
    private const DISK_ROOT = 'app/private/';
    private const PER_PAGE  = 20;

    // ─── Listagem ─────────────────────────────────────────────────────────────

    public function listarPendentes(): LengthAwarePaginator
    {
        return Pagamento::where("status", "proof_submitted")
            ->with("pedido")
            ->orderBy("proof_submitted_at", "asc")
            ->paginate(self::PER_PAGE);
    }

    // ─── Comprovativo ─────────────────────────────────────────────────────────

    public function obterComprovativo(Pagamento $pagamento): BinaryFileResponse
    {
        $path = $this->resolverCaminhoComprovativo($pagamento);

        return response()->file($path, $this->headersComprovativo($path));
    }

    private function resolverCaminhoComprovativo(Pagamento $pagamento): string
    {
        if (!$pagamento->proof_path) {
            abort(404, "Comprovativo não encontrado.");
        }

        $path = storage_path(self::DISK_ROOT . $pagamento->proof_path);

        if (!file_exists($path)) {
            abort(404, "Comprovativo não encontrado.");
        }

        return $path;
    }

    private function headersComprovativo(string $path): array
    {
        return [
            'Content-Type'        => mime_content_type($path),
            'Content-Disposition' => 'inline; filename="comprovativo"',
        ];
    }

    // ─── Submissão ────────────────────────────────────────────────────────────

    public function enviarComprovativo(Pedido $pedido, array $dados): void
    {
        DB::transaction(function () use ($pedido, $dados) {
            // Lock pessimista no Pedido: impede que duas submissões concorrentes
            // do mesmo comprovativo (ex. duplo-clique, ou duas abas abertas)
            // criem dois registos de Pagamento em paralelo via obterOuCriarPagamento().
            $pedidoLocked = Pedido::whereKey($pedido->id)->lockForUpdate()->firstOrFail();

            $caminho   = $this->guardarFicheiroComprovativo($pedidoLocked, $dados);
            $pagamento = $this->obterOuCriarPagamento($pedidoLocked);

            $this->registarComprovativo($pagamento, $caminho, $dados);
            $this->atualizarEstadoPedidoAguardaComprovativo($pedidoLocked);

            Log::info("Comprovativo enviado", ["pedido_id" => $pedidoLocked->id]);
        });
    }

    private function guardarFicheiroComprovativo(Pedido $pedido, array $dados): string
    {
        return $dados["comprovativo"]->store("comprovativos/{$pedido->id}", "private");
    }

    /**
     * Obtém o pagamento existente ou cria um novo com o valor lido da BD.
     *
     * SEGURANÇA: o valor nunca é hardcoded — vem de ConfiguracaoPagamento.
     * Se o tipo de documento não tiver configuração activa, lança
     * ModelNotFoundException e a transacção faz rollback automaticamente.
     */
    private function obterOuCriarPagamento(Pedido $pedido): Pagamento
    {
        if ($pedido->pagamento) {
            return $pedido->pagamento;
        }

        $config = ConfiguracaoPagamento::obterParaTipo($pedido->document_type);

        return $pedido->pagamento()->create([
            "status"   => "pending",
            "amount"   => $config->valor,
            "currency" => "AOA",
        ]);
    }

    private function registarComprovativo(Pagamento $pagamento, string $caminho, array $dados): void
    {
        $pagamento->update([
            "proof_path"         => $caminho,
            "proof_hash"         => hash_file("sha256", $dados["comprovativo"]->path()),
            "proof_submitted_at" => now(),
            "status"             => "proof_submitted",
        ]);
    }

    private function atualizarEstadoPedidoAguardaComprovativo(Pedido $pedido): void
    {
        $status = $pedido->status instanceof \UnitEnum
            ? $pedido->status->value
            : (string) $pedido->status;

        if ($status === 'nao_pago') {
            $pedido->atualizarStatus(EstadoPedido::AGUARDA_COMPROVATIVO);
        }
    }

    // ─── Aprovação ────────────────────────────────────────────────────────────

    /**
     * @throws \DomainException se o pagamento já não estiver em 'proof_submitted'
     *         (ex. segunda tentativa de aprovar, ou aprovação concorrente).
     */
    public function aprovar(Pagamento $pagamento): void
    {
        DB::transaction(function () use ($pagamento) {
            // Lock pessimista: relê o pagamento com FOR UPDATE, garantindo que
            // duas aprovações concorrentes do mesmo registo não passam ambas
            // pela verificação de estado antes de qualquer uma escrever.
            $pagamentoLocked = Pagamento::whereKey($pagamento->id)->lockForUpdate()->firstOrFail();

            $this->garantirPagamentoPendente($pagamentoLocked);

            $pedidoLocked = Pedido::whereKey($pagamentoLocked->application_id)->lockForUpdate()->firstOrFail();

            $this->confirmarPagamento($pagamentoLocked);
            $this->atualizarEstadoPedidoConfirmado($pedidoLocked);
            $this->dispararEventoPagamentoConfirmado($pedidoLocked);

            Log::info("Pagamento aprovado", [
                "pagamento_id"   => $pagamentoLocked->id,
                "pedido_id"      => $pedidoLocked->id,
                "confirmado_por" => auth()->id(),
            ]);
        });
    }

    private function garantirPagamentoPendente(Pagamento $pagamento): void
    {
        if ($pagamento->status !== 'proof_submitted') {
            throw new \DomainException(
                "Este pagamento já foi processado anteriormente (estado actual: {$pagamento->status})."
            );
        }
    }

    private function confirmarPagamento(Pagamento $pagamento): void
    {
        $pagamento->update([
            "status"       => "confirmed",
            "confirmed_at" => now(),
            "confirmed_by" => auth()->id(),
        ]);
    }

    private function atualizarEstadoPedidoConfirmado(Pedido $pedido): void
    {
        $pedido->atualizarStatus(EstadoPedido::PAGAMENTO_CONFIRMADO, auth()->user());
    }

    private function dispararEventoPagamentoConfirmado(Pedido $pedido): void
    {
        event(new PagamentoConfirmado($pedido));
    }

    // ─── Rejeição ─────────────────────────────────────────────────────────────

    /**
     * @throws \DomainException se o pagamento já não estiver em 'proof_submitted'.
     */
    public function rejeitar(Pagamento $pagamento, string $motivo): void
    {
        DB::transaction(function () use ($pagamento, $motivo) {
            $pagamentoLocked = Pagamento::whereKey($pagamento->id)->lockForUpdate()->firstOrFail();

            $this->garantirPagamentoPendente($pagamentoLocked);

            $pedidoLocked = Pedido::whereKey($pagamentoLocked->application_id)->lockForUpdate()->firstOrFail();

            $this->registarRejeicao($pagamentoLocked, $motivo);
            $this->atualizarEstadoPedidoRejeitado($pedidoLocked, $motivo);

            Log::warning("Pagamento rejeitado", [
                "pagamento_id"  => $pagamentoLocked->id,
                "pedido_id"     => $pedidoLocked->id,
                "motivo"        => $motivo,
                "rejeitado_por" => auth()->id(),
            ]);
        });
    }

    private function registarRejeicao(Pagamento $pagamento, string $motivo): void
    {
        $pagamento->update([
            "status"           => "rejected",
            "rejection_reason" => $motivo,
        ]);
    }

    private function atualizarEstadoPedidoRejeitado(Pedido $pedido, string $motivo): void
    {
        $pedido->atualizarStatus(EstadoPedido::NAO_PAGO, auth()->user(), [
            "motivo_rejeicao" => $motivo,
        ]);
    }
}