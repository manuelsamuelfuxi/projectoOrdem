<?php

namespace App\Services\Publico;

use App\Models\Pedido;
use App\Models\Documento;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\EstadoPedido;

class ConsultaService
{
    // =========================================================
    // BUSCA POR BI
    // =========================================================

    public function buscarPorBi(string $bi_number): ?Pedido
    {
        $bi_number = strtoupper(trim($bi_number));

        return Pedido::where('bi_number', $bi_number)->first()
            ?? Pedido::where('bi_number', ltrim($bi_number, '0'))->first();
    }

    public function buscarDocumentosPorBi(string $bi_number): array
    {
        $pedido = $this->buscarPorBi($bi_number);

        if (!$pedido) {
            return [];
        }

        return [
            'pedido'     => $pedido,
            'documentos' => $pedido->documentos()->get(),
        ];
    }

    // =========================================================
    // BUSCA POR ID
    // =========================================================

    public function buscarPedido(string $referenceUuid): Pedido
    {
        return Pedido::where('reference_uuid', $referenceUuid)->firstOrFail();
    }

    public function baixarDocumento(string $referenceUuid, string $tipo): BinaryFileResponse
    {
        $pedido = $this->buscarPedido($referenceUuid);

        $documento = $pedido->documentos()->where('type', $tipo)->firstOrFail();

        $path = storage_path('app/private/' . $documento->stored_path);

        if (!file_exists($path)) {
            abort(404, 'Ficheiro do documento não encontrado.');
        }

        return response()->download($path, $documento->original_name);
    }

    // =========================================================
    // FICHA DE COBRANÇA PDF
    // =========================================================

    public function baixarFichaCobranca(string $referenceUuid): Response
    {
        $pedido = $this->buscarPedido($referenceUuid);

        $logoBase64 = $this->carregarLogoBase64();

        $pdf = Pdf::loadView('pdf.ficha-cobranca', compact('pedido', 'logoBase64'));

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'margin_left'          => 0,
            'margin_right'         => 0,
            'margin_top'           => 0,
            'margin_bottom'        => 0,
        ]);

        $safeProcessNumber = str_replace(['/', '\\'], '-', $pedido->process_number);

        return $pdf->download("Ficha_Cobranca_{$safeProcessNumber}.pdf");
    }

    // =========================================================
    // MÉTODOS PRIVADOS
    // =========================================================

    private function carregarLogoBase64(): string
    {
        $logoPath = public_path('images/logo.jpg');

        if (!file_exists($logoPath)) {
            Log::warning('Logo não encontrado para geração de PDF.', ['path' => $logoPath]);
            return '';
        }

        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
    }

public function baixarCarteira(string $referenceUuid): BinaryFileResponse
{
    $pedido = $this->buscarPedido($referenceUuid);

    if ($pedido->status !== EstadoPedido::DOCUMENTO_EMITIDO) {
        abort(404, 'A carteira profissional ainda não foi emitida para este pedido.');
    }

    $path = storage_path("app/documents/{$pedido->id}.pdf");

    if (!file_exists($path)) {
        abort(404, 'Ficheiro da carteira profissional não encontrado.');
    }

    $safeProcessNumber = str_replace(['/', '\\'], '-', $pedido->process_number);

    return response()->download($path, "Carteira_Profissional_{$safeProcessNumber}.pdf");
}
}