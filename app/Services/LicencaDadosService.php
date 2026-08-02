<?php

namespace App\Services;

use App\Enums\TipoDocumento;
use App\Models\Application;
use Carbon\Carbon;

class LicencaDadosService
{
    /**
     * Única responsabilidade: transformar um Application num array de
     * dados já formatados e prontos para a view do PDF. Nenhuma lógica de
     * renderização de PDF ou de QR code vive aqui.
     */

    private const CATEGORIA_PADRAO = 'Téc. Diagnóstico Terapêutica 2ª Classe';

    public function prepararDados(Application $pedido): array
    {
        $validadeAte = $pedido->professional_license_expiry
            ?? Carbon::now()->addYear();

        return [
            'nomeCompleto'   => $pedido->full_name,
            'categoria'      => self::CATEGORIA_PADRAO,
            'curso'          => $pedido->curso->nome ?? '',
            'biNumero'       => $pedido->bi_number,
            'funcao'         => $pedido->funcao->nome ?? 'Membro efectivo',
            'nacionalidade'  => $pedido->nationality,
            'provincia'      => $pedido->provincia->nome ?? '',
            'numeroProcesso' => $pedido->process_number,
            'validadeAte'    => $validadeAte->format('d/m/Y'),
            'urlVerificacao' => $this->urlVerificacao($pedido),
            'fotoBase64'     => $this->fotoBase64($pedido),
        ];
    }

    private function categoria(Application $pedido): string
    {
        $curso  = $pedido->curso->nome ?? '';
        $classe = $pedido->classe_label ?? '';

        return trim("Téc. {$curso} {$classe}");
    }

    private function urlVerificacao(Application $pedido): string
    {
        return rtrim(config('app.url'), '/') . '/verificar-licenca/' . $pedido->reference_uuid;
    }

    /**
     * A foto é guardada como Documento (tipo foto_identificacao), não como
     * coluna da Application — por isso vai buscar-se pela relação
     * documentos(). Devolve null se não existir; a view trata esse caso.
     */
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