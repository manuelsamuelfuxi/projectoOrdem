<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreviewLicencaRequest;
use App\Models\Application;
use App\Services\LicencaDadosService;
use App\Services\LicencaPdfService;

class LicencaController extends Controller
{
    private LicencaDadosService $licencaDadosService;
    private LicencaPdfService $licencaPdfService;

    public function __construct(LicencaDadosService $licencaDadosService, LicencaPdfService $licencaPdfService)
    {
        $this->licencaDadosService = $licencaDadosService;
        $this->licencaPdfService = $licencaPdfService;
    }

    /**
     * Pré-visualização do PDF da licença — SÓ LEITURA. Não grava nada na
     * BD, não altera o status do pedido, não dispara nenhum evento.
     */
    public function preview(PreviewLicencaRequest $request, Application $pedido)
    {
        $dados = $this->licencaDadosService->prepararDados($pedido);
        $pdf = $this->licencaPdfService->gerar($dados);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="licenca-preview.pdf"',
        ]);
    }

    public function visualizarDocumento(Application $pedido)
    {
        if ($pedido->status !== \App\Enums\EstadoPedido::DOCUMENTO_EMITIDO->value) {
            abort(404, 'Documento ainda não emitido.');
        }

        $caminho = storage_path("app/documents/{$pedido->id}.pdf");

        if (!file_exists($caminho)) {
            abort(404, 'Ficheiro do documento não encontrado.');
        }

        return response()->file($caminho, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}