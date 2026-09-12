<?php

namespace App\Services\Licenca;

use App\Enums\TipoDocumento;
use App\Models\Application;
use Carbon\Carbon;

class LicencaDadosService
{
    /**
     * Única responsabilidade: transformar um Application num array de
     * dados já formatados e prontos para a view do PDF da Licença.
     */
    public function prepararDados(Application $pedido): array
    {
        $validadeAte = $pedido->professional_license_expiry
            ?? Carbon::now()->addYear();

        return [
            'nomeCompleto'   => $pedido->full_name,
            'curso'          => $this->curso($pedido),
            'escola'         => $pedido->institution,
            'nacionalidade'  => $pedido->nationality,
            'numeroProcesso' => $pedido->process_number,
            'validadeAte'    => $validadeAte->format('d/m/Y'),
            'urlVerificacao' => $this->urlVerificacao($pedido),
            'fotoBase64'     => $this->fotoBase64($pedido),
        ];
    }

    private function curso(Application $pedido): string
    {
        $curso  = $pedido->curso->nome ?? '';
        $classe = $pedido->classe_label ?? '';

        return trim("{$curso}/ {$classe}");
    }

    private function urlVerificacao(Application $pedido): string
    {
        return rtrim(config('app.url'), '/') . '/verificar-licenca/' . $pedido->reference_uuid;
    }

    private function fotoBase64(Application $pedido): ?string
    {
        $documento = $pedido->documentos()
            ->where('type', TipoDocumento::FOTO_IDENTIFICACAO->value)
            ->first();

        if (!$documento) {
            return null;
        }

        $caminho = storage_path('app/private/' . $documento->stored_path);

        if (!file_exists($caminho)) {
            return null;
        }

        $mime = $documento->mime_type ?? 'image/jpeg';

        return "data:{$mime};base64," . base64_encode(file_get_contents($caminho));
    }
}