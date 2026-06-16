@extends("layouts.app")

@section("title", "Pedido de " . ($tipoDocumento === "carteira" ? "Carteira Profissional" : "Licença Profissional") . " - Passo 4 de 4")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Resumo e Ficha de Cobrança</h4>
            </div>
            <div class="card-body">

                {{-- Progresso --}}
                <div class="mb-4">
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;">
                            Passo 4/4: Finalizar Pedido
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Pré-visualização do Pedido</strong><br>
                    Confirme os dados antes de finalizar.
                </div>

                {{-- Dados do Candidato --}}
                <h5 class="mb-3">Dados do Candidato</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nome Completo</th>
                        <td>{{ $dadosCandidato['nome_completo'] }}</td>
                    </tr>
                    <tr>
                        <th>Data de Nascimento</th>
                        <td>{{ \Carbon\Carbon::parse($dadosCandidato['data_nascimento'])->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>BI / Número</th>
                        <td>{{ $dadosCandidato['numero_bi'] }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $dadosCandidato['email'] }}</td>
                    </tr>
                    <tr>
                        <th>Telefone</th>
                        <td>{{ $dadosCandidato['telefone'] }}</td>
                    </tr>
                </table>

                {{-- Dados Profissionais --}}
                <h5 class="mb-3 mt-4">Dados Profissionais</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Categoria Profissional</th>
                        <td>{{ $dadosProfissionais['categoria_profissional'] }}</td>
                    </tr>
                    @if(!empty($dadosProfissionais['especializacao']))
                    <tr>
                        <th>Nível</th>
                        <td>{{ ucfirst($dadosProfissionais['especializacao']) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Instituição de Formação</th>
                        <td>{{ $dadosProfissionais['instituicao_formacao'] }}</td>
                    </tr>
                    @if(!empty($dadosProfissionais['funcao']))
                    <tr>
                        <th>Função</th>
                        <td>{{ $dadosProfissionais['funcao'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($dadosProfissionais['sector']))
                    <tr>
                        <th>Sector</th>
                        <td>{{ $dadosProfissionais['sector'] }}</td>
                    </tr>
                    @endif
                </table>

                {{-- Informação de Pagamento --}}
                <h5 class="mb-3 mt-4">Informação de Pagamento</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Documento Solicitado</th>
                        <td>{{ $tipoDocumento === 'carteira' ? 'Carteira Profissional' : 'Licença Profissional' }}</td>
                    </tr>
                    <tr>
                        <th>Valor a Pagar</th>
                        <td><strong class="text-success">KZ {{ number_format($valor, 2, ',', '.') }}</strong></td>
                    </tr>
                </table>

                {{-- Dados Bancários --}}
                <div class="alert alert-info">
                    <h6><i class="fas fa-university"></i> Dados Bancários para Transferência</h6>
                    <p class="mb-0">
                        <strong>Banco:</strong> Banco Angolano de Investimentos (BAI)<br>
                        <strong>Titular:</strong> Ordem dos Técnicos de Diagnóstico e Terapeutas de Angola<br>
                        <strong>NIB:</strong> 0001 1234 5678 9012 3456 7<br>
                        <strong>SWIFT:</strong> BAIAAOLUXXX
                    </p>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Aviso:</strong> O pedido só será processado após a confirmação do pagamento.
                    Envie o comprovativo através do link de consulta que receberá por email.
                </div>

                {{-- Formulário de submissão --}}
                <form method="POST" action="{{ route('pedido.submeter') }}" id="formFinal">
                    @csrf

                    <div class="form-check mb-3">
                        <input type="checkbox"
                               class="form-check-input"
                               id="confirmacao"
                               name="confirmacao"
                               value="1">
                        <label class="form-check-label" for="confirmacao">
                            Declaro que todas as informações fornecidas são verdadeiras e estou ciente das
                            implicações legais de informações falsas.
                        </label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pedido.upload-documentos', ['tipo' => $tipoDocumento]) }}"
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-success btn-lg" id="btnFinalizar">
                            <i class="fas fa-check-circle"></i> Finalizar Pedido
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push("scripts")
<script>
document.getElementById("formFinal").addEventListener("submit", function (e) {
    const confirmacao = document.getElementById("confirmacao");

    if (!confirmacao.checked) {
        e.preventDefault();
        alert("Deve confirmar que as informações são verdadeiras antes de prosseguir.");
        return;
    }

    const btn = document.getElementById("btnFinalizar");
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
});
</script>
@endpush

@endsection