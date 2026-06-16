@extends("layouts.admin")

@section("title", "Configurações do Sistema")
@section("page-title", "Configurações do Sistema")

@section("content")
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Valores dos Documentos</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route("super-admin.configuracoes.valores") }}">
                    @csrf
                    <div class="mb-3">
                        <label for="valor_carteira" class="form-label">Valor da Carteira Profissional (KZ)</label>
                        <input type="number" step="0.01" class="form-control" id="valor_carteira" name="valor_carteira" 
                               value="{{ $configuracoes["valor_carteira"] ?? 50000 }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="valor_licenca" class="form-label">Valor da Licença Profissional (KZ)</label>
                        <input type="number" step="0.01" class="form-control" id="valor_licenca" name="valor_licenca" 
                               value="{{ $configuracoes["valor_licenca"] ?? 75000 }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Valores</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dados Bancários</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route("super-admin.configuracoes.bancos") }}">
                    @csrf
                    <div class="mb-3">
                        <label for="banco" class="form-label">Banco</label>
                        <input type="text" class="form-control" id="banco" name="banco" 
                               value="{{ $configuracoes["dados_bancarios"]["banco"] ?? "BAI" }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="conta" class="form-label">Número da Conta</label>
                        <input type="text" class="form-control" id="conta" name="conta" 
                               value="{{ $configuracoes["dados_bancarios"]["conta"] ?? "000123456789" }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="iban" class="form-label">IBAN</label>
                        <input type="text" class="form-control" id="iban" name="iban" 
                               value="{{ $configuracoes["dados_bancarios"]["iban"] ?? "AO060000123456789" }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Dados Bancários</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Backup do Sistema</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route("super-admin.configuracoes.backup") }}">
                    @csrf
                    <p>Gere um backup completo do banco de dados e arquivos do sistema.</p>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-database"></i> Gerar Backup Agora
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection