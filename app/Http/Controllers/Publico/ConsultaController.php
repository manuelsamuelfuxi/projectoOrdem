<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultarPedidoRequest;
use App\Http\Requests\EnviarComprovativoRequest;
use App\Services\ConsultaService;
use App\Services\PagamentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConsultaController extends Controller
{
    public function __construct(
        private ConsultaService $consultaService,
        private PagamentoService $pagamentoService,
    ) {}

    public function form(): View
    {
        return view("publico.consulta.form");
    }

    public function consultar(ConsultarPedidoRequest $request): View|RedirectResponse
    {
        $resultado = $this->consultaService->buscarDocumentosPorBi($request->bi_number);

        if (empty($resultado)) {
            return redirect()
                ->route("consulta.form")
                ->with("error", "Nenhum pedido encontrado com o número de BI: " . $request->bi_number);
        }

        return redirect()->route("consulta.estado", $resultado['pedido']->id);
    }

    public function estado(int $id): View
    {
        $pedido = $this->consultaService->buscarPedido($id);

        return view("publico.consulta.estado", compact("pedido"));
    }

    public function formUpload(int $id): View
    {
        $pedido = $this->consultaService->buscarPedido($id);

        return view("publico.consulta.upload-comprovativo", compact("pedido"));
    }

    public function enviarComprovativo(EnviarComprovativoRequest $request, int $id): RedirectResponse
    {
        $pedido = $this->consultaService->buscarPedido($id);

        $this->pagamentoService->enviarComprovativo($pedido, $request->validated());

        return redirect()
            ->route("consulta.estado", $id)
            ->with("success", "Comprovativo enviado com sucesso! Aguarde a confirmação.");
    }

    public function baixarDocumento(int $id, string $tipo): BinaryFileResponse
    {
        return $this->consultaService->baixarDocumento($id, $tipo);
    }

    public function baixarFichaCobranca(int $id): Response
    {
        return $this->consultaService->baixarFichaCobranca($id);
    }
}