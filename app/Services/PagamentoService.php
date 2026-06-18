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
            $caminho   = $this->guardarFicheiroComprovativo($pedido, $dados);
            $pagamento = $this->obterOuCriarPagamento($pedido);

            $this->registarComprovativo($pagamento, $caminho, $dados);
            $this->atualizarEstadoPedidoAguardaComprovativo($pedido);

            Log::info("Comprovativo enviado", ["pedido_id" => $pedido->id]);
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

    public function aprovar(Pagamento $pagamento): void
    {
        DB::transaction(function () use ($pagamento) {
            $this->confirmarPagamento($pagamento);
            $this->atualizarEstadoPedidoConfirmado($pagamento);
            $this->dispararEventoPagamentoConfirmado($pagamento);

            Log::info("Pagamento aprovado", [
                "pagamento_id"   => $pagamento->id,
                "pedido_id"      => $pagamento->pedido->id,
                "confirmado_por" => auth()->id(),
            ]);
        });
    }

    private function confirmarPagamento(Pagamento $pagamento): void
    {
        $pagamento->update([
            "status"       => "confirmed",
            "confirmed_at" => now(),
            "confirmed_by" => auth()->id(),
        ]);
    }

    private function atualizarEstadoPedidoConfirmado(Pagamento $pagamento): void
    {
        $pagamento->pedido->atualizarStatus(EstadoPedido::PAGAMENTO_CONFIRMADO, auth()->user());
    }

    private function dispararEventoPagamentoConfirmado(Pagamento $pagamento): void
    {
        event(new PagamentoConfirmado($pagamento->pedido));
    }

    // ─── Rejeição ─────────────────────────────────────────────────────────────

    public function rejeitar(Pagamento $pagamento, string $motivo): void
    {
        DB::transaction(function () use ($pagamento, $motivo) {
            $this->registarRejeicao($pagamento, $motivo);
            $this->atualizarEstadoPedidoRejeitado($pagamento, $motivo);

            Log::warning("Pagamento rejeitado", [
                "pagamento_id"  => $pagamento->id,
                "pedido_id"     => $pagamento->pedido->id,
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

    private function atualizarEstadoPedidoRejeitado(Pagamento $pagamento, string $motivo): void
    {
        $pagamento->pedido->atualizarStatus(EstadoPedido::NAO_PAGO, auth()->user(), [
            "motivo_rejeicao" => $motivo,
        ]);
    }
}