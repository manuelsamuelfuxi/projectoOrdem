<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ConfiguracaoPagamento;
use App\Models\Curso;
use App\Models\Documento;
use App\Models\Funcao;
use App\Models\Municipio;
use App\Models\Pagamento;
use App\Models\Provincia;
use App\Http\Requests\Etapa1Request;
use App\Http\Requests\Etapa2Request;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PedidoService
{
    // =========================================================
    // PREPARAÇÃO DAS ETAPAS
    // =========================================================

    public function prepararEtapa1(Etapa1Request $request): array
    {
        $dados = $request->validated();

        if ($request->hasFile('foto')) {
            $dados['foto_path'] = $request->file('foto')->store('temp/fotos', 'public');
        }

        // IMPORTANTE: remover o ficheiro binário antes de guardar em sessão.
        // Objetos UploadedFile não são serializáveis de forma segura na sessão
        // e corrompem o array dados_etapa1, fazendo foto_path "desaparecer".
        unset($dados['foto']);

        return $dados;
    }

    public function prepararEtapa2(Etapa2Request $request): array
    {
        return $request->validated();
    }

    public function prepararEtapa3(Request $request): array
    {
        $validated = $request->validate([
            'tipo_documento_upload' => 'required|string',
            'arquivo'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $tipo    = $validated['tipo_documento_upload'];
        $arquivo = $validated['arquivo'];
        $caminho = $arquivo->store('temp/uploads', 'public');

        $documentos        = Session::get('documentos_enviados', []);
        $documentos[$tipo] = [
            'path'          => $caminho,
            'nome_original' => $arquivo->getClientOriginalName(),
            'tamanho'       => $arquivo->getSize(),
            'mime_type'     => $arquivo->getMimeType(),
        ];

        return $documentos;
    }

    public function removerDocumento(string $tipo, array $documentos): array
    {
        if (!isset($documentos[$tipo])) {
            return [false, 'Documento não encontrado.'];
        }

        $caminho = $documentos[$tipo]['path'];
        if (Storage::disk('public')->exists($caminho)) {
            Storage::disk('public')->delete($caminho);
        }

        unset($documentos[$tipo]);
        Session::put('documentos_enviados', $documentos);

        return [true, 'Documento removido.'];
    }

    public function documentosActuais(): array
    {
        return Session::get('documentos_enviados', []);
    }

    public function validarDocumentosObrigatorios(array $documentos): array
    {
        $obrigatorios = ['bi', 'certificado_habilitacoes'];
        $faltando     = array_diff($obrigatorios, array_keys($documentos));

        if (!empty($faltando)) {
            return [false, 'Ainda faltam documentos obrigatórios: ' . implode(', ', $faltando)];
        }

        return [true, null];
    }

    /**
     * Prepara todos os dados para a etapa 4 (ficha de cobrança / revisão).
     *
     * SEGURANÇA: o valor e os dados bancários vêm exclusivamente da BD através
     * de ConfiguracaoPagamento::obterParaTipo(). Se o tipo de documento não
     * existir na tabela ou estiver inactivo, lança ModelNotFoundException — o
     * controller trata esse caso e nunca mostra dados inconsistentes ao utilizador.
     *
     * A $fotoUrl é gerada aqui (server-side) e nunca na view, evitando expor
     * caminhos físicos do servidor e garantindo que só URLs de ficheiros
     * efectivamente existentes chegam ao template.
     */
    public function prepararFichaCobranca(array $dadosEtapa1, array $dadosEtapa2, array $documentos): array
    {
        // ── Configuração de pagamento vinda da BD ──────────────────────────
        $tipoDocumento        = $dadosEtapa1['tipo_documento'];
        $configuracaoPagamento = ConfiguracaoPagamento::obterParaTipo($tipoDocumento);

        // ── URL da foto (gerada server-side) ───────────────────────────────
        $fotoUrl = null;
        $fotoPath = $dadosEtapa1['foto_path'] ?? null;

        if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
$disk    = Storage::disk('public');
$fotoUrl = $disk->url($fotoPath);
        }

        // ── Dados do candidato enriquecidos com nomes de BD ────────────────
        $dadosCandidato                   = $dadosEtapa1;
        $dadosCandidato['provincia_nome'] = Provincia::find($dadosEtapa1['provincia_id'])?->nome;
        $dadosCandidato['municipio_nome'] = Municipio::find($dadosEtapa1['municipio_id'])?->nome;

        // ── Dados profissionais enriquecidos com nomes de BD ───────────────
        $dadosProfissionais               = $dadosEtapa2;
        $dadosProfissionais['curso_nome'] = Curso::find($dadosEtapa2['curso_id'])?->nome;
        $dadosProfissionais['classe_label'] = $this->formatarClasse(
            $dadosEtapa2['nivel'],
            $dadosEtapa2['classe']
        );

        if (!empty($dadosEtapa2['funcao_id'])) {
            $dadosProfissionais['funcao_nome'] = Funcao::find($dadosEtapa2['funcao_id'])?->nome;
        }
        if (!empty($dadosEtapa2['provincia_trabalho_id'])) {
            $dadosProfissionais['provincia_trabalho_nome'] = Provincia::find($dadosEtapa2['provincia_trabalho_id'])?->nome;
        }
        if (!empty($dadosEtapa2['municipio_trabalho_id'])) {
            $dadosProfissionais['municipio_trabalho_nome'] = Municipio::find($dadosEtapa2['municipio_trabalho_id'])?->nome;
        }

        return [
            'tipoDocumento'         => $tipoDocumento,
            'dadosCandidato'        => $dadosCandidato,
            'dadosProfissionais'    => $dadosProfissionais,
            'documentosEnviados'    => $documentos,
            'fotoUrl'               => $fotoUrl,

            // Array simples — a view nunca acede ao Model directamente,
            // o que evita lazy-loading acidental e expõe apenas o necessário.
            'configuracaoPagamento' => [
                'valor'        => $configuracaoPagamento->valor,
                'banco'        => $configuracaoPagamento->banco,
                'iban'         => $configuracaoPagamento->iban,
                'beneficiario' => $configuracaoPagamento->beneficiario,
                'nif'          => $configuracaoPagamento->nif,
            ],
        ];
    }

    /**
     * Formata a classe/ano de acordo com o nível académico para exibição.
     */
    private function formatarClasse(string $nivel, string $classe): string
    {
        return match ($nivel) {
            'medio'    => $classe . 'ª Classe',
            'superior' => str_replace('ano', 'º Ano', $classe),
            default    => $classe,
        };
    }

    // =========================================================
    // SUBMISSÃO FINAL
    // =========================================================

    public function submeter(array $dadosEtapa1, array $dadosEtapa2, array $documentosSessao): Application
    {
        DB::beginTransaction();

        try {
            $tipoDocumento = $dadosEtapa1['tipo_documento'];
            $documentos    = $this->resolverFicheiros($dadosEtapa1, $documentosSessao);

            $this->garantirDocumentosObrigatorios($documentos, $tipoDocumento);

            $pedido = $this->criarPedido($dadosEtapa1, $dadosEtapa2);
            $this->salvarDocumentos($pedido, $documentos);
            $this->criarPagamento($pedido);

            DB::commit();

            Log::info('Pedido submetido com sucesso', [
                'pedido_id'      => $pedido->id,
                'process_number' => $pedido->process_number,
            ]);

            return $pedido;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao submeter pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    // =========================================================
    // MÉTODOS PRIVADOS — SUBMISSÃO
    // =========================================================

    private function resolverFicheiros(array $dadosEtapa1, array $documentosSessao): array
    {
        $documentos = [];

        if (isset($dadosEtapa1['foto_path'])) {
            $caminho = storage_path('app/public/' . $dadosEtapa1['foto_path']);
            if (file_exists($caminho)) {
                $documentos['foto_identificacao'] = new UploadedFile(
                    $caminho, 'foto_identificacao.jpg', 'image/jpeg', null, true
                );
            }
        }

        foreach ($documentosSessao as $tipo => $info) {
            $caminho = storage_path('app/public/' . $info['path']);
            if (file_exists($caminho)) {
                $documentos[$tipo] = new UploadedFile(
                    $caminho, $info['nome_original'], $info['mime_type'], null, true
                );
            }
        }

        return $documentos;
    }

    private function garantirDocumentosObrigatorios(array $documentos, string $tipoDocumento): void
    {
        // A foto de identificação é opcional — o campo não é obrigatório na etapa 1
        // e é tratada separadamente em resolverFicheiros().
        // Os únicos documentos obrigatórios para todos os tipos são BI e Certificado.
        $obrigatorios = ['bi', 'certificado_habilitacoes'];

        $faltando = array_diff($obrigatorios, array_keys($documentos));

        if (!empty($faltando)) {
            throw new \InvalidArgumentException(
                'Documentos obrigatórios em falta: ' . implode(', ', $faltando)
            );
        }
    }

    private function criarPedido(array $dadosEtapa1, array $dadosEtapa2): Application
    {
        return Application::create([
            // Etapa 1 — pessoais
            'full_name'                  => $dadosEtapa1['nome_completo'],
            'birth_date'                 => $dadosEtapa1['data_nascimento'],
            'gender'                     => $dadosEtapa1['genero'],
            'nationality'                => $dadosEtapa1['nacionalidade'],
            'bi_number'                  => $dadosEtapa1['numero_bi'],
            'email'                      => $dadosEtapa1['email'],
            'phone'                      => $dadosEtapa1['telefone'],
            'alternative_phone'          => $dadosEtapa1['telefone_alternativo'] ?? null,
            'provincia_id'               => $dadosEtapa1['provincia_id'],
            'municipio_id'               => $dadosEtapa1['municipio_id'],
            'bairro'                     => $dadosEtapa1['bairro'],
            'document_type'              => $dadosEtapa1['tipo_documento'],

            // Etapa 2 — académicos (obrigatórios)
            'institution'                => $dadosEtapa2['instituicao_formacao'],
            'curso_id'                   => $dadosEtapa2['curso_id'],
            'nivel'                      => $dadosEtapa2['nivel'],
            'classe'                     => $dadosEtapa2['classe'],

            // Etapa 2 — profissionais (opcionais)
            'funcao_id'                  => $dadosEtapa2['funcao_id'] ?? null,
            'nome_instituicao_trabalho'  => $dadosEtapa2['nome_instituicao'] ?? null,
            'sector'                     => $dadosEtapa2['sector'] ?? null,
            'provincia_trabalho_id'      => $dadosEtapa2['provincia_trabalho_id'] ?? null,
            'municipio_trabalho_id'      => $dadosEtapa2['municipio_trabalho_id'] ?? null,
            'telefone_trabalho'          => $dadosEtapa2['telefone_trabalho'] ?? null,
            'email_trabalho'             => $dadosEtapa2['email_trabalho'] ?? null,

            'status'     => 'nao_pago',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function salvarDocumentos(Application $pedido, array $documentos): void
    {
        foreach ($documentos as $tipo => $arquivo) {
            if ($this->documentoDuplicado($arquivo)) {
                Log::warning('Documento duplicado ignorado', [
                    'tipo'          => $tipo,
                    'original_name' => $arquivo->getClientOriginalName(),
                ]);
                continue;
            }
            $this->guardarDocumento($pedido, $tipo, $arquivo);
        }
    }

    private function documentoDuplicado(UploadedFile $arquivo): bool
    {
        return Documento::where('hash_sha256', hash_file('sha256', $arquivo->path()))->exists();
    }

    private function guardarDocumento(Application $pedido, string $tipo, UploadedFile $arquivo): void
    {
        Documento::create([
            'application_id' => $pedido->id,
            'document_uuid'  => (string) str()->uuid(),
            'type'           => $tipo,
            'original_name'  => $arquivo->getClientOriginalName(),
            'stored_path'    => $arquivo->store("documentos/{$pedido->id}", 'private'),
            'hash_sha256'    => hash_file('sha256', $arquivo->path()),
            'mime_type'      => $arquivo->getMimeType(),
            'file_size'      => $arquivo->getSize(),
        ]);
    }

    /**
     * Cria o registo de pagamento com o valor lido da BD.
     *
     * SEGURANÇA: o valor nunca vem do request nem está hardcoded.
     * ConfiguracaoPagamento::obterParaTipo() lança ModelNotFoundException
     * se o tipo não existir — a transacção faz rollback automaticamente.
     */
    private function criarPagamento(Application $pedido): void
    {
        $config = ConfiguracaoPagamento::obterParaTipo($pedido->document_type);

        Pagamento::create([
            'application_id'    => $pedido->id,
            'payment_uuid'      => (string) str()->uuid(),
            'payment_reference' => 'REF-' . strtoupper(uniqid()),
            'amount'            => $config->valor,
            'currency'          => 'AOA',
            'status'            => 'pending',
        ]);
    }

    // =========================================================
    // EMISSÃO / REJEIÇÃO (Super Admin)
    // =========================================================

    public function aprovarEmissao(Application $pedido): void
    {
        DB::beginTransaction();
        try {
            $pedido->update(['status' => 'documento_emitido', 'document_issued_at' => now()]);
            DB::commit();
            Log::info('Documento emitido', ['pedido_id' => $pedido->id, 'por' => auth()->id()]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao emitir documento', ['pedido_id' => $pedido->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function rejeitar(Application $pedido, string $motivo): void
    {
        DB::beginTransaction();
        try {
            $pedido->update(['status' => 'rejeitado', 'admin_notes' => $motivo]);
            DB::commit();
            Log::info('Pedido rejeitado', ['pedido_id' => $pedido->id, 'por' => auth()->id(), 'motivo' => $motivo]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao rejeitar pedido', ['pedido_id' => $pedido->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}