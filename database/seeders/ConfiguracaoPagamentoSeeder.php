<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Popula a tabela configuracoes_pagamento com os dados reais da ORDEPDITA.
 *
 * Para actualizar valores ou dados bancários em produção:
 *   UPDATE configuracoes_pagamento SET valor = X WHERE tipo_documento = 'carteira';
 * Nunca alterar no código-fonte — apenas na BD.
 */
class ConfiguracaoPagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $registos = [
            [
                'tipo_documento' => 'carteira',
                'valor'          => 50000,
                'banco'          => 'Banco de Fomento de Angola (BFA)',
                'iban'           => 'AO06 0044 0000 0123 4567 8901',
                'beneficiario'   => 'Ordem dos Profissionais de Diagnóstico e Terapêutica de Angola (ORDEPDITA)',
                'nif'            => '5417256890',
                'ativo'          => true,
            ],
            [
                'tipo_documento' => 'licenca',
                'valor'          => 75000,
                'banco'          => 'Banco de Fomento de Angola (BFA)',
                'iban'           => 'AO06 0044 0000 0123 4567 8901',
                'beneficiario'   => 'Ordem dos Profissionais de Diagnóstico e Terapêutica de Angola (ORDEPDITA)',
                'nif'            => '5417256890',
                'ativo'          => true,
            ],
        ];

        foreach ($registos as $registo) {
            DB::table('configuracoes_pagamento')->updateOrInsert(
                ['tipo_documento' => $registo['tipo_documento']], // chave de upsert
                array_merge($registo, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
