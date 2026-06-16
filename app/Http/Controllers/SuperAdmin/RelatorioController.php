<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Pagamento;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function completos()
    {
        $pedidosPorMes = Pedido::selectRaw("DATE_FORMAT(created_at, \"%Y-%m\") as mes, COUNT(*) as total")
            ->groupBy("mes")
            ->orderBy("mes", "desc")
            ->get();
        
        $receitaPorMes = Pagamento::selectRaw("DATE_FORMAT(confirmed_at, \"%Y-%m\") as mes, SUM(amount) as total")
            ->where("status", "confirmed")
            ->groupBy("mes")
            ->orderBy("mes", "desc")
            ->get();
        
        return view("super-admin.relatorios.completos", compact("pedidosPorMes", "receitaPorMes"));
    }

    public function auditoria()
    {
        $historico = \App\Models\HistoricoStatus::with("pedido", "alteradoPor")
            ->orderBy("created_at", "desc")
            ->paginate(50);
        
        return view("super-admin.relatorios.auditoria", compact("historico"));
    }

    public function exportar($formato)
    {
        // TODO: Implementar exportação para Excel/PDF
        return response()->json(["message" => "Exportação em breve"], 200);
    }
}