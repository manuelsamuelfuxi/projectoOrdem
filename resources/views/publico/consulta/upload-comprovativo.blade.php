@extends("layouts.app")

@section("title", "Enviar Comprovativo de Pagamento")

@push('styles')
    @vite('resources/css/consulta/enviarcomprovativo.css')
@endpush

@section("content")
<div class="status-wrap">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="ficha-cobranca">

                <div class="doc-header">
                    <div class="doc-logo-area">
                        <div class="doc-emblema">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Logotipo ORDEPDITA">
                        </div>
                        <div class="doc-instituicao">
                            <h1>Comprovativo</h1>
                            <p>Envio de comprovativo de pagamento</p>
                        </div>
                    </div>
                    <div class="doc-titulo-area">
                        <div class="doc-ref">
                            <span><strong>Processo:</strong> {{ $pedido->process_number }}</span>
                            <span><strong>BI:</strong> {{ $pedido->bi_number }}</span>
                        </div>
                    </div>
                </div>

                <div class="doc-corpo">
                    <form method="POST" action="{{ route('pedido.enviar-comprovativo', $pedido->reference_uuid) }}" enctype="multipart/form-data" id="form-comprovativo">
                        @csrf

                        <div class="doc-secao">
                            <div class="doc-secao-titulo">
                                <i class="fas fa-cloud-upload-alt"></i> Documento
                            </div>
                            <div class="doc-grid">
                                <div class="doc-campo">
                                    <label for="comprovativo" class="doc-label">Comprovativo de Pagamento *</label>
                                    <input type="file"
                                           class="form-control @error('comprovativo') is-invalid @enderror"
                                           id="comprovativo"
                                           name="comprovativo"
                                           required
                                           accept=".jpg,.jpeg,.png,.pdf">
                                    @error('comprovativo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Formatos aceites: JPG, PNG, PDF. Máximo 5MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="document-actions" style="margin-top: 40px;">
                            <button type="submit" class="btn-acao btn-pagar" style="width: auto; padding-left: 24px;">
                                <i class="fas fa-paper-plane"></i> Submeter Comprovativo
                            </button>
                            <a href="{{ route('consulta.estado', $pedido->reference_uuid) }}" class="btn-acao btn-voltar-inferior">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>

                    </form>
                </div>

                <div class="doc-rodape">
                    <p>Confirme os dados antes de enviar. O comprovativo será analisado pela secretaria.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection