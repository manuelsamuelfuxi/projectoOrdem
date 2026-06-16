@extends("layouts.app")

@section("title", "Consultar Estado do Pedido")

@push('styles')
@vite('resources/css/consulta/consulta-form.css')
@endpush

@section("content")
<div class="consulta-wrap">

    <div class="consulta-card">
        <div class="consulta-topo">
            
            <div class="consulta-info">
                <div class="consulta-info-titulo">Consultar Pedido</div>
                <div class="consulta-info-linha"></div>
                
            </div>
        </div>

        @if(session("error"))
        <div class="consulta-mensagens">
            <div class="consulta-alerta erro">
                <i class="fas fa-times-circle"></i>
                <div class="consulta-alerta-content">
                    <div class="consulta-alerta-title">Erro</div>
                    <p class="consulta-alerta-message">{{ session("error") }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session("success"))
        <div class="consulta-mensagens">
            <div class="consulta-alerta sucesso">
                <i class="fas fa-check-circle"></i>
                <div class="consulta-alerta-content">
                    <div class="consulta-alerta-title">Sucesso</div>
                    <p class="consulta-alerta-message">{{ session("success") }}</p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route("consulta.consultar") }}">
            @csrf
            
            <div class="consulta-form">
                <div class="consulta-form-titulo">
                    <i class="fas fa-id-card"></i> Dados da Consulta
                </div>

                <div class="consulta-campo">
                    <label class="consulta-label">
                        Número do Bilhete de Identidade <span class="consulta-obrigatorio">*</span>
                    </label>
                    <input type="text" 
                           class="consulta-input @error("bi_number") erro @enderror" 
                           id="bi_number" 
                           name="bi_number" 
                           value="{{ old("bi_number") }}" 
                           placeholder="Exemplo: 007587082LA045"
                           maxlength="20"
                           autocomplete="off">
                    @error("bi_number")
                        <div class="consulta-erro">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @else
                        <div class="consulta-help">
                            <i class="fas fa-info-circle"></i> Digite o mesmo número usado no momento do pedido
                        </div>
                    @enderror
                </div>
            </div>

            <div class="consulta-rodape">
                <a href="{{ url('/') }}" class="consulta-btn-voltar">
                    <i class="fas fa-home"></i> Página Inicial
                </a>
                <button type="submit" class="consulta-btn-consultar">
                    <i class="fas fa-search"></i> Consultar Pedido
                </button>
            </div>
        </form>
    </div>
</div>
@endsection