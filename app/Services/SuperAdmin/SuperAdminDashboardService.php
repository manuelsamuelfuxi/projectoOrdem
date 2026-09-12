<?php

namespace App\Services\SuperAdmin;

use App\Models\User;
use App\Models\Application;
use App\Models\Pagamento;
use Illuminate\Support\Collection;

class SuperAdminDashboardService
{
    private const PEDIDOS_PARA_EMITIR_LIMITE = 10;
    private const ADMINS_RECENTES_LIMITE     = 5;

    // ─── Administradores ────────────────────────────────────────────────

    public function totalAdmins(): int
    {
        return User::where('role', 'admin')->count();
    }

    public function adminsRecentes(): Collection
    {
        return User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->limit(self::ADMINS_RECENTES_LIMITE)
            ->get();
    }

    // ─── Pedidos ────────────────────────────────────────────────────────

    /**
     * Total de pedidos cujo PAGAMENTO já foi aprovado por um admin.
     *
     * DECISÃO DE DESIGN: conta pela tabela `pagamentos` (status = 'confirmed'),
     * não por `applications.status`. A aprovação do pagamento é um facto
     * histórico e imutável — uma vez confirmado, o pedido pode continuar a
     * evoluir (em_analise, aprovado, documento_emitido) ou até ser rejeitado
     * mais tarde na fase de análise, mas o facto "o admin aprovou este
     * pagamento" nunca deixa de ser verdade. Contar por status da Application
     * faria este número oscilar (subir e descer) consoante o estado actual
     * do pedido, o que não corresponde à métrica pedida.
     *
     * Reflecte-se automaticamente no dashboard assim que a página é
     * recarregada — sem necessidade de eventos, filas ou websockets,
     * porque é sempre uma leitura fresca da BD.
     */
    public function totalPedidos(): int
    {
        return Pagamento::where('status', 'confirmed')->count();
    }

    public function pedidosAprovados(): int
    {
        return Application::where('status', 'aprovado')->count();
    }

    public function pedidosComDocumentosEmitidos(): int
    {
        return Application::where('status', 'documento_emitido')->count();
    }

    public function pedidosParaEmitir(): Collection
    {
        return Application::where('status', 'aprovado')
            ->orderBy('approved_at', 'asc')
            ->limit(self::PEDIDOS_PARA_EMITIR_LIMITE)
            ->get();
    }

    // ─── Financeiro ─────────────────────────────────────────────────────

    public function receitaTotal(): float
    {
        return (float) Pagamento::where('status', 'confirmed')->sum('amount');
    }
}