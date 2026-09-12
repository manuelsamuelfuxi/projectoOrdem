<?php

namespace App\Http\Controllers\Publico;

use App\Enums\TipoDocumento;
use App\Http\Controllers\Controller;
use App\Http\Requests\Etapa1Request;
use App\Http\Requests\Etapa2Request;
use App\Http\Requests\Etapa3Request;
use App\Services\Publico\PedidoService;
use App\Services\Publico\ProvinciaMunicipioService;
use App\Models\Curso;
use App\Models\Funcao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Support\Modal;

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

    public function formCartaoMembro()
    {
        Session::forget(['dados_etapa1', 'dados_etapa2', 'documentos_enviados']);

        return view('publico.pedido.etapa1-dados-pessoais', [
            'tipoDocumento' => 'cartao_membro',
            'titulo'        => 'Pedido de Cartão de Membro',
            'provincias'    => $this->provinciaService->todasProvincias(),
        ]);
    }

    public function salvarEtapa1(Etapa1Request $request)
    {
        // DEBUG TEMPORÁRIO
        Log::channel('stderr')->info('[DEBUG Etapa1]', [
            'has_file'     => $request->hasFile('foto'),
            'files'        => array_keys($request->allFiles()),
            'content_type' => $request->header('Content-Type'),
        ]);

        $dados = $this->pedidoService->prepararEtapa1($request);
        Session::put('dados_etapa1', $dados);

        return redirect()->route('pedido.dados-profissionais', [
            'tipo' => $request->validated('tipo_documento'),
        ]);
    }

    // ── Etapa 2 ───────────────────────────────────────────────────────────────

    public function dadosProfissionais(Request $request)
    {
        if (!Session::has('dados_etapa1')) {
            return redirect()->route($this->rotaFormulario())
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
            return redirect()->route($this->rotaFormulario())
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
            return redirect()->route($this->rotaFormulario())
                ->with('error', 'Complete as etapas anteriores primeiro.');
        }

        return view('publico.pedido.etapa3-upload-documentos', [
            'tipoDocumento'      => $request->get('tipo', Session::get('dados_etapa1.tipo_documento')),
            'documentosEnviados' => Session::get('documentos_enviados', []),
        ]);
    }

    public function salvarEtapa3(Etapa3Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2')) {
            return response()->json(['error' => 'Complete as etapas anteriores primeiro.'], 400);
        }

        Session::put('documentos_enviados', $this->pedidoService->prepararEtapa3($request));

        return response()->json(['success' => true, 'message' => 'Documento enviado com sucesso!']);
    }

    public function removerDocumento(Request $request)
    {
        $tipo = TipoDocumento::tryFrom($request->input('tipo'));

        if ($tipo === null) {
            return response()->json(['error' => 'Tipo de documento inválido.'], 422);
        }

        [$sucesso, $mensagem] = $this->pedidoService->removerDocumento(
            $tipo,
            Session::get('documentos_enviados', [])
        );

        if (!$sucesso) {
            return response()->json(['error' => $mensagem], 404);
        }

        Session::put('documentos_enviados', $this->pedidoService->documentosActuais());

        return response()->json(['success' => true, 'message' => $mensagem]);
    }

    // ── Etapa 4 (ficha de cobrança / revisão) ────────────────────────────────

    public function fichaCobranca(Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2')) {
            return redirect()->route($this->rotaFormulario())
                ->with('error', 'Complete todas as etapas primeiro.');
        }

        [$podeProsseguir, $erro] = $this->pedidoService->validarDocumentosObrigatorios(
            Session::get('documentos_enviados', [])
        );

        if (!$podeProsseguir) {
            return redirect()->route('pedido.upload-documentos')->with('error', $erro);
        }

        try {
            $dados = $this->pedidoService->prepararFichaCobranca(
                Session::get('dados_etapa1'),
                Session::get('dados_etapa2'),
                Session::get('documentos_enviados', [])
            );
        } catch (ModelNotFoundException $e) {
            // A tabela configuracoes_pagamento não tem registo para este tipo de documento.
            // Situação de erro de configuração — não expor detalhes ao utilizador.
            Log::critical('Configuração de pagamento em falta na BD', [
                'tipo_documento' => Session::get('dados_etapa1.tipo_documento'),
                'error'          => $e->getMessage(),
            ]);

            return redirect()->route($this->rotaFormulario())
                ->with('error', 'Não foi possível carregar a ficha de pagamento. Por favor contacte o suporte.');
        }

        return view('publico.pedido.etapa4-ficha-cobranca', $dados);
    }

    // ── Submissão — chamada pelo modal da etapa 4 ────────────────────────────

    public function submeter(Request $request)
    {
        if (!Session::has('dados_etapa1') || !Session::has('dados_etapa2') || !Session::has('documentos_enviados')) {
            Modal::aviso(
                'Sessão incompleta',
                'Parece que a sua sessão expirou ou faltam dados de uma etapa anterior. Por favor, comece o pedido novamente.'
            );
            return redirect()->route($this->rotaFormulario());
        }

        try {
            $pedido = $this->pedidoService->submeter(
                Session::get('dados_etapa1'),
                Session::get('dados_etapa2'),
                Session::get('documentos_enviados')
            );

        } catch (ModelNotFoundException $e) {
            Log::critical('Configuração de pagamento em falta durante submissão', [
                'tipo_documento' => Session::get('dados_etapa1.tipo_documento'),
                'error'          => $e->getMessage(),
            ]);

            Modal::erro(
                'Configuração indisponível',
                'Ainda não é possível processar este tipo de pedido — a configuração de pagamento não está definida. A nossa equipa já foi notificada.'
            );
            return redirect()->back();

        } catch (\InvalidArgumentException $e) {
            Modal::aviso(
                'Documentos em falta',
                $e->getMessage() ?: 'Faltam documentos obrigatórios para concluir o pedido.'
            );
            return redirect()->route('pedido.upload-documentos');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('Erro de BD na submissão do pedido', ['error' => $e->getMessage()]);

            // 23000 = violação de constraint (duplicado, chave estrangeira em falta, etc.)
            if ($e->getCode() === '23000') {
                $mensagem = str_contains($e->getMessage(), 'bi_number')
                    ? 'Já existe um pedido registado com este número de BI.'
                    : (str_contains($e->getMessage(), 'email')
                        ? 'Já existe um pedido registado com este endereço de email.'
                        : 'Já existe um registo com estes dados. Verifique a informação submetida.');

                Modal::aviso('Pedido duplicado', $mensagem);
                return redirect()->back();
            }

            Modal::erro(
                'Erro ao guardar o pedido',
                'Não foi possível gravar o seu pedido na base de dados. Por favor tente novamente dentro de alguns minutos.'
            );
            return redirect()->back();

        } catch (\Throwable $e) {
            $refErro = strtoupper(substr(md5(uniqid()), 0, 8));

            Log::error("Erro inesperado na submissão do pedido [ref: {$refErro}]", [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            Modal::erro(
                'Algo correu mal',
                "Ocorreu um erro inesperado ao processar o seu pedido. Se o problema persistir, contacte o suporte indicando o código <strong>{$refErro}</strong>."
            );
            return redirect()->back();
        }

        Session::forget(['dados_etapa1', 'dados_etapa2', 'documentos_enviados']);

        Modal::sucesso(
            'Pedido submetido com sucesso!',
            'O seu pedido foi registado. Vai ser redireccionado para acompanhar o estado do processo.'
        );

        return redirect()->route('consulta.estado', ['uuid' => $pedido->reference_uuid]);
    }

    // ── Helper — resolve a rota do formulário certo consoante o tipo ────────────

    private function rotaFormulario(): string
    {
        return match (Session::get('dados_etapa1.tipo_documento')) {
            'licenca'       => 'pedido.licenca.form',
            'cartao_membro' => 'pedido.cartao-membro.form',
            'carteira'      => 'pedido.carteira.form',
            default         => 'home',
        };
    }
}