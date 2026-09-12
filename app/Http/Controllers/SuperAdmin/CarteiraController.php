<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreviewCarteiraRequest;
use App\Models\Application;
use App\Services\Carteira\CarteiraDadosService;
use App\Services\Carteira\CarteiraPdfService;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use App\Enums\EstadoPedido;

class CarteiraController extends Controller
{
    private CarteiraDadosService $carteiraDadosService;
    private CarteiraPdfService $carteiraPdfService;

    public function __construct(CarteiraDadosService $carteiraDadosService, CarteiraPdfService $carteiraPdfService)
    {
        $this->carteiraDadosService = $carteiraDadosService;
        $this->carteiraPdfService = $carteiraPdfService;
    }

   public function preview(Application $pedido)
    {
        $dados = $this->carteiraDadosService->prepararDados($pedido);
        $pdf = $this->carteiraPdfService->gerar($dados);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="carteira-preview.pdf"',
        ]);
    }


    public function visualizarDocumento(Application $pedido)
    {
        if ($pedido->status !== EstadoPedido::DOCUMENTO_EMITIDO->value) {
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

    /**
     * Gera, na hora, o preview da carteira de cada pedido aprovado
     * financeiramente e ainda não emitido (mesmo universo da tela
     * "aprovados-financeiramente"), juntando tudo num único PDF
     * mostrado inline no navegador — igual ao botão "Ver", só que
     * concatenado para todos de uma vez.
     */
    public function visualizarTodas()
    {
        $pedidos = Application::where('status', 'pagamento_confirmado')
            ->with('pagamento')
            ->orderByDesc('updated_at')
            ->get();

        if ($pedidos->isEmpty()) {
            abort(404, 'Não há pedidos aprovados pendentes de emissão neste momento.');
        }

        $pdf = new Fpdi();

        foreach ($pedidos as $pedido) {
            $dados = $this->carteiraDadosService->prepararDados($pedido);
            $conteudoPdf = $this->carteiraPdfService->gerar($dados);

            $totalPaginas = $pdf->setSourceFile(
                StreamReader::createByString($conteudoPdf)
            );

            for ($pagina = 1; $pagina <= $totalPaginas; $pagina++) {
                $template = $pdf->importPage($pagina);
                $tamanho  = $pdf->getTemplateSize($template);

                $pdf->AddPage(
                    $tamanho['height'] > $tamanho['width'] ? 'P' : 'L',
                    [$tamanho['width'], $tamanho['height']]
                );
                $pdf->useTemplate($template);
            }
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Todas_Licencas_Aprovadas.pdf"',
        ]);
    }
}