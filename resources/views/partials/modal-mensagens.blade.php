@if(session('modal'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: @json(session('modal.tipo', 'info')),
        title: @json(session('modal.titulo', '')),
        html: @json(session('modal.mensagem', '')),
        confirmButtonText: @json(session('modal.botao', 'Entendi')),
        confirmButtonColor: '#0c4a8b',
        allowOutsideClick: @json(session('modal.tipo') !== 'error'),
    });
});
</script>
@endif

{{-- Compatibilidade: se algum sítio ainda usar with('error', '...') simples --}}
@if(session('error') && !session('modal'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({ icon: 'error', title: 'Ocorreu um problema', text: @json(session('error')), confirmButtonColor: '#0c4a8b' });
});
</script>
@endif