@extends("layouts.admin")

@section("title", "Relatório de Pedidos - Administração")

@section("content")
<div class="row">
    <div class="col-md-12">
        <h1 class="h2 mb-4">Relatório de Pedidos</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Pedidos</h5>
                        <h2 class="mb-0">{{ $estatisticas["total"] ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-file-alt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Aguardando Pagamento</h5>
                        <h2 class="mb-0">{{ $estatisticas["aguardando_pagamento"] ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Comprovativos Aguardando</h5>
                        <h2 class="mb-0">{{ $estatisticas["aguardando_comprovativo"] ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-upload fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Aprovados</h5>
                        <h2 class="mb-0">{{ $estatisticas["aprovados"] ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.relatorios.pedidos') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                               value="{{ request('data_inicio') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control"
                               value="{{ request('data_fim') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="nao_pago"               {{ request('status') === 'nao_pago'               ? 'selected' : '' }}>Não Pago</option>
                            <option value="aguarda_comprovativo"   {{ request('status') === 'aguarda_comprovativo'   ? 'selected' : '' }}>Aguardando Comprovativo</option>
                            <option value="pagamento_confirmado"   {{ request('status') === 'pagamento_confirmado'   ? 'selected' : '' }}>Pagamento Confirmado</option>
                            <option value="aprovado"               {{ request('status') === 'aprovado'               ? 'selected' : '' }}>Aprovado</option>
                            <option value="documento_emitido"      {{ request('status') === 'documento_emitido'      ? 'selected' : '' }}>Documento Emitido</option>
                            <option value="cancelado"              {{ request('status') === 'cancelado'              ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.relatorios.pedidos') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lista de Pedidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Processo</th>
                                <th>Candidato</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Valor</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->process_number }}</td>
                                <td>{{ $pedido->full_name }}</td>
                                <td>{{ $pedido->email }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'nao_pago'             => 'secondary',
                                            'aguarda_comprovativo' => 'warning',
                                            'pagamento_confirmado' => 'info',
                                            'aprovado'             => 'success',
                                            'documento_emitido'    => 'primary',
                                            'cancelado'            => 'danger',
                                        ];
                                        $class = $statusClass[$pedido->status->value] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $class }}">
                                        {{ $pedido->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    {{ $pedido->pagamento
                                        ? number_format($pedido->pagamento->valor, 2, ',', '.') . ' KZ'
                                        : '—' }}
                                </td>
                                <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Nenhum pedido encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection