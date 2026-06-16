<?php

namespace App\Jobs;

use App\Models\Pedido;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GerarFichaCobrancaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public Pedido $pedido;
    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function handle(): void
    {
        Log::info("GerarFichaCobrancaJob: Iniciado", [
            "pedido_id" => $this->pedido->id,
            "referencia" => $this->pedido->reference_uuid
        ]);

        // Dados para o PDF
        $dados = [
            "pedido" => $this->pedido,
            "pagamento" => $this->pedido->pagamento,
            "valor" => $this->pedido->pagamento->amount,
            "referencia_pagamento" => $this->pedido->pagamento->payment_reference,
            "data_emissao" => now(),
            "dados_bancarios" => [
                "banco" => "Banco Angolano de Investimentos (BAI)",
                "titular" => "Ordem dos Técnicos de Diagnóstico e Terapeutas de Angola",
                "nib" => "0001 1234 5678 9012 3456 7",
                "swift" => "BAIAAOLUXXX"
            ]
        ];

        // Gerar PDF
        $pdf = Pdf::loadView("pdfs.ficha-cobranca", $dados);
        
        // Salvar PDF
        $caminho = "fichas-cobranca/ficha_{$this->pedido->reference_uuid}.pdf";
        Storage::disk("private")->put($caminho, $pdf->output());
        
        // Salvar caminho no pedido (precisamos adicionar campo na migration depois)
        // $this->pedido->update(["ficha_cobranca_path" => $caminho]);

        Log::info("GerarFichaCobrancaJob: Finalizado", [
            "pedido_id" => $this->pedido->id,
            "caminho" => $caminho
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GerarFichaCobrancaJob: Falhou", [
            "pedido_id" => $this->pedido->id,
            "erro" => $exception->getMessage()
        ]);
    }
}