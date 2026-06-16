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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GerarDocumentoFinalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public Pedido $pedido;
    public string $tipoDocumento;
    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(Pedido $pedido, string $tipoDocumento)
    {
        $this->pedido = $pedido;
        $this->tipoDocumento = $tipoDocumento;
    }

    public function handle(): void
    {
        Log::info("GerarDocumentoFinalJob: Iniciado", [
            "pedido_id" => $this->pedido->id,
            "tipo_documento" => $this->tipoDocumento
        ]);

        // Gerar QR Code com dados verificáveis
        $qrCodeData = json_encode([
            "processo" => $this->pedido->process_number,
            "nome" => $this->pedido->full_name,
            "tipo" => $this->tipoDocumento,
            "data_emissao" => now()->format("Y-m-d"),
            "validade" => now()->addYears(5)->format("Y-m-d")
        ]);
        
        $qrCode = QrCode::format("png")->size(200)->generate($qrCodeData);
        $qrCodePath = "temp/qrcode_{$this->pedido->reference_uuid}.png";
        Storage::disk("public")->put($qrCodePath, $qrCode);

        // Dados para o PDF
        $dados = [
            "pedido" => $this->pedido,
            "tipo_documento" => $this->tipoDocumento,
            "numero_registro" => strtoupper(substr($this->pedido->reference_uuid, 0, 8)),
            "data_emissao" => now(),
            "data_validade" => now()->addYears(5),
            "qr_code_path" => Storage::disk("public")->path($qrCodePath),
            "assinatura_digital" => hash("sha256", $qrCodeData . config("app.key"))
        ];

        // Gerar PDF
        $view = $this->tipoDocumento === "carteira" 
            ? "pdfs.carteira-profissional" 
            : "pdfs.licenca-profissional";
            
        $pdf = Pdf::loadView($view, $dados);
        
        // Salvar PDF
        $caminho = "documentos-finais/{$this->tipoDocumento}_{$this->pedido->reference_uuid}.pdf";
        Storage::disk("private")->put($caminho, $pdf->output());
        
        // Limpar QR Code temporário
        Storage::disk("public")->delete($qrCodePath);

        // Salvar caminho no pedido (precisamos adicionar campo na migration depois)
        // $campo = $this->tipoDocumento === "carteira" ? "carteira_path" : "licenca_path";
        // $this->pedido->update([$campo => $caminho]);

        Log::info("GerarDocumentoFinalJob: Finalizado", [
            "pedido_id" => $this->pedido->id,
            "caminho" => $caminho
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GerarDocumentoFinalJob: Falhou", [
            "pedido_id" => $this->pedido->id,
            "tipo_documento" => $this->tipoDocumento,
            "erro" => $exception->getMessage()
        ]);
    }
}