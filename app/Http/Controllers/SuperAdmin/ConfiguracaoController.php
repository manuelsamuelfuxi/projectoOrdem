<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        // TODO: Buscar configurações do banco ou arquivo
        $configuracoes = [
            "valor_carteira" => 50000,
            "valor_licenca" => 75000,
            "dados_bancarios" => [
                "banco" => "BAI",
                "conta" => "000123456789",
                "iban" => "AO060000123456789"
            ]
        ];
        
        return view("super-admin.configuracoes.index", compact("configuracoes"));
    }

    public function atualizarValores(Request $request)
    {
        $request->validate([
            "valor_carteira" => "required|numeric|min:0",
            "valor_licenca" => "required|numeric|min:0",
        ]);
        
        // TODO: Salvar configurações no banco ou arquivo
        // settings()->set("valores.carteira", $request->valor_carteira);
        
        return redirect()
            ->route("super-admin.configuracoes.index")
            ->with("success", "Valores atualizados com sucesso!");
    }

    public function atualizarDadosBancarios(Request $request)
    {
        $request->validate([
            "banco" => "required|string|max:100",
            "conta" => "required|string|max:50",
            "iban" => "required|string|max:34",
        ]);
        
        // TODO: Salvar dados bancários
        
        return redirect()
            ->route("super-admin.configuracoes.index")
            ->with("success", "Dados bancários atualizados!");
    }

    public function gerarBackup()
    {
        // TODO: Implementar backup do banco de dados
        // Artisan::call("backup:run");
        
        return redirect()
            ->route("super-admin.configuracoes.index")
            ->with("success", "Backup gerado com sucesso!");
    }
}