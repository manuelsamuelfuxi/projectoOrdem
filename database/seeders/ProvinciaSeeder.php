<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provincia;

class ProvinciaSeeder extends Seeder
{
    public function run(): void
    {
        $provincias = [
            'Bengo',
            'Benguela',
            'Bié',
            'Cabinda',
            'Cuando',
            'Cubango',
            'Cuanza Norte',
            'Cuanza Sul',
            'Cunene',
            'Huambo',
            'Huíla',
            'Icolo e Bengo',
            'Luanda',
            'Lunda Norte',
            'Lunda Sul',
            'Malanje',
            'Moxico',
            'Moxico Leste',
            'Namibe',
            'Uíge',
            'Zaire',
        ];

        foreach ($provincias as $nome) {
            Provincia::firstOrCreate(['nome' => $nome]);
        }
    }
}
