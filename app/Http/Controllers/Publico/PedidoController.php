<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Etapa1Request;
use App\Http\Requests\Etapa2Request;
use App\Services\PedidoService;
use App\Services\ProvinciaMunicipioService;
use App\Models\Curso;
use App\Models\Funcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PedidoController extends Controller
{
    public function __construct(
        private PedidoService $pedidoService,
        private ProvinciaMunicipioService $provinciaService,
    ) {}

    // ── Etapa 1 ───────────────────────────────────────────────────────────────

    public function formCarteira()
    {
        Session::forget(['dados_etapa1', 'dados_etapa2', 'documentos_enviados']);

        return view('publico.pedido.etapa1-dados-pessoais', [
            'tipoDocumento' => 'carteira',
            'titulo'        => 'Pedido de Carteira Profissional',
            'provincias'    => $this->provinciaService->todasProvincias(),
        ]);
    }

    public function formLicenca()
    {
        Session::forget(['dados_etapa1', 'dados_etapa2', 'documentos_enviados']);

        return view('publico.pedido.etapa1-dados-pessoais', [
            'tipoDocumento' => 'licenca',
            'titulo'        => 'Pedido de Licença Profissional',
            'provincias'    => $this->provinciaService->todasProvincias(),
        ]);
    }

    public function salvarEtapa1(Etapa1Request $request)
    {
        Session::put('dados_etapa1', $this->pedidoService->prepararEtapa1($request));

        return redirect()->route('pedido.dados-profissionais', [
            'tipo' => $request->validated('tipo_documento'),
        ]);
    }

    // ── Etapa 2 ───────────────────────────────────────────────────────────────

    public function dadosProfissionais(Request $request)
    {
        if (!Session::has('dados_etapa1')) {
            return redirect()->route('pedido.carteira.form')
                ->with('error', 'Complete a etapa 1 primeiro.');
        }

        return view('publico.pedido.etapa2-dados-profissionais', [
            'tipoDocumento'   => $request->get('tipo', Session::get('dados_etapa1.tipo_documento')),
            'dadosAnteriores' => Session::get('dados_etapa2', []),
            'provincias'      => $this->provinciaService->todasProvincias(),
            'cursos'          => Curso::orderBy('nome')->get(),
            'funcoes'         => Funcao::orderBy('nome')->get(),
        ]);
    }

    public function salvarEtapa2(Etapa2Request $request)
    {
        if (!Session::has('dados_etapa1')) {
            return redirect()->route('pedido.carteira.form')
                ->with('error', 'Complete a etapa 1 primeiro.');
        }

        Session::put('dados_etapa2', $this->pedidoService->prepararEtapa2($request));

        return redirect()->route('pedido.upload-documentos', [
            'tipo' => Session::get('dados_etapa1.tipo_documento'),
        ]);
    }

    // ── AJAX — Municípios por Província ───────────────────────────────────────

    public function municipiosPorProvincia(int $provinciaId)
    {
        $municipios = $this->provinciaService->municipiosDaProvincia($provinciaId);

        return response()->json($municipios->map(fn($m) => [
            'id'   => $m->id,
            'nome' => $m->nome,
        ]));
    }

    // ── Etapa 3 ───────────────────────────────────────────────────────────────

    public function uploadDocumentos(Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2')) {
            return redirect()->route('pedido.carteira.form')
                ->with('error', 'Complete as etapas anteriores primeiro.');
        }

        return view('publico.pedido.etapa3-upload-documentos', [
            'tipoDocumento'      => $request->get('tipo', Session::get('dados_etapa1.tipo_documento')),
            'documentosEnviados' => Session::get('documentos_enviados', []),
        ]);
    }

    public function salvarEtapa3(Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2')) {
            return response()->json(['error' => 'Complete as etapas anteriores primeiro.'], 400);
        }

        Session::put('documentos_enviados', $this->pedidoService->prepararEtapa3($request));

        return response()->json(['success' => true, 'message' => 'Documento enviado com sucesso!']);
    }

    public function removerDocumento(Request $request)
    {
        [$sucesso, $mensagem] = $this->pedidoService->removerDocumento(
            $request->input('tipo'),
            Session::get('documentos_enviados', [])
        );

        if (!$sucesso) {
            return response()->json(['error' => $mensagem], 404);
        }

        Session::put('documentos_enviados', $this->pedidoService->documentosActuais());

        return response()->json(['success' => true, 'message' => $mensagem]);
    }

    // ── Submissão — chamada ao clicar "Próximo" na etapa 3 ───────────────────

    public function submeter(Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2') || !Session::has('documentos_enviados')) {
            return redirect()->route('pedido.carteira.form')
                ->with('error', 'Complete todas as etapas primeiro.');
        }

        [$podeProsseguir, $erro] = $this->pedidoService->validarDocumentosObrigatorios(
            Session::get('documentos_enviados', [])
        );

        if (!$podeProsseguir) {
            return redirect()->route('pedido.upload-documentos')->with('error', $erro);
        }

        $pedido = $this->pedidoService->submeter(
            Session::get('dados_etapa1'),
            Session::get('dados_etapa2'),
            Session::get('documentos_enviados')
        );

        Session::forget(['dados_etapa1', 'dados_etapa2', 'documentos_enviados']);

        return redirect()->route('consulta.estado', ['id' => $pedido->id]);
    }

    // ── Etapa 4 (ficha de cobrança — mantida para compatibilidade) ────────────

    public function fichaCobranca(Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2')) {
            return redirect()->route('pedido.carteira.form')
                ->with('error', 'Complete todas as etapas primeiro.');
        }

        [$podeProsseguir, $erro] = $this->pedidoService->validarDocumentosObrigatorios(
            Session::get('documentos_enviados', [])
        );

        if (!$podeProsseguir) {
            return redirect()->route('pedido.upload-documentos')->with('error', $erro);
        }

        return view('publico.pedido.etapa4-ficha-cobranca',
            $this->pedidoService->prepararFichaCobranca(
                Session::get('dados_etapa1'),
                Session::get('dados_etapa2'),
                Session::get('documentos_enviados', [])
            )
        );
    }
}