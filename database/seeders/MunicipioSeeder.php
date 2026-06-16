<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provincia;
use App\Models\Municipio;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $dados = [
            'Bengo' => [
                'Ambriz', 'Bula Atumba', 'Dande', 'Dembos-Quibaxe',
                'Nambuangongo', 'Pango Aluquém',
            ],
            'Benguela' => [
                'Baía Farta', 'Balombo', 'Benguela', 'Bocoio', 'Caimbambo',
                'Catumbela', 'Chongorói', 'Cubal', 'Ganda', 'Lobito',
            ],
            'Bié' => [
                'Andulo', 'Camacupa', 'Catabola', 'Chinguar', 'Chitembo',
                'Cuemba', 'Cunhinga', 'Cuíto', 'Nharea',
            ],
            'Cabinda' => [
                'Belize', 'Buco-Zau', 'Cabinda', 'Cacongo',
            ],
            'Cuando' => [
                'Calai', 'Cuangar', 'Cuchi', 'Mavinga', 'Nancova', 'Rivungo',
            ],
            'Cubango' => [
                'Cuito Cuanavale', 'Dirico', 'Longa', 'Menongue',
            ],
            'Cuanza Norte' => [
                'Ambaca', 'Banga', 'Bolongongo', 'Cambambe', 'Cazengo',
                'Golungo Alto', 'Gonguembo', 'Lucala', 'Ndalatando',
                'Quiculungo', 'Samba Cajú',
            ],
            'Cuanza Sul' => [
                'Amboim', 'Cassongue', 'Cela', 'Conda', 'Ebo', 'Libolo',
                'Mussende', 'Porto Amboim', 'Quibala', 'Quilenda',
                'Seles', 'Sumbe', 'Waku-Kungo',
            ],
            'Cunene' => [
                'Cahama', 'Cuanhama', 'Curoca', 'Cuvelai',
                'Namacunde', 'Ombadja',
            ],
            'Huambo' => [
                'Bailundo', 'Caála', 'Katchiungo', 'Chicala-Choloanga',
                'Ekunha', 'Huambo', 'Londuimbale', 'Longonjo',
                'Mungo', 'Tchindjendje', 'Ucuma',
            ],
            'Huíla' => [
                'Caconda', 'Cacula', 'Caluquembe', 'Chiange', 'Chibia',
                'Chicomba', 'Chipindo', 'Cuvango', 'Gambos', 'Humpata',
                'Jamba', 'Lubango', 'Matala', 'Quilengues', 'Quipungo',
            ],
            'Icolo e Bengo' => [
                'Bom Jesus', 'Cabiri', 'Cabo Ledo', 'Calumbo',
                'Catete', 'Quiçama', 'Sequele',
            ],
            'Luanda' => [
                'Belas', 'Cacuaco', 'Cazenga', 'Kilamba Kiaxi',
                'Luanda', 'Quilamba Quiaxi', 'Talatona', 'Viana',
                'Ingombota', 'Maianga', 'Rangel', 'Sambizanga',
                'Samba', 'Camama', 'Mussulo', 'Hoji-ya-Henda',
            ],
            'Lunda Norte' => [
                'Cambulo', 'Capenda-Camulemba', 'Caungula', 'Chitato',
                'Cuango', 'Cuílo', 'Lóvua', 'Lubalo', 'Lucapa', 'Xá-Muteba',
            ],
            'Lunda Sul' => [
                'Cacolo', 'Dala', 'Muconda', 'Saurimo',
            ],
            'Malanje' => [
                'Cacuso', 'Calandula', 'Cambundi-Catembo', 'Cangandala',
                'Caombo', 'Cuaba Nzoji', 'Cunda-Dia-Baze', 'Kiwaba Nzoji',
                'Luquembo', 'Malanje', 'Marimba', 'Massango',
                'Mucari', 'Quela', 'Quirima',
            ],
            'Moxico' => [
                'Alto Zambeze', 'Camanongue', 'Cameia', 'Leua',
                'Luacano', 'Luau', 'Luena', 'Lumbala Nguimbo',
                'Bundas',
            ],
            'Moxico Leste' => [
                'Cazombo', 'Lumbala N\'guimbo', 'Alto Cuílo',
            ],
            'Namibe' => [
                'Bibala', 'Camacuio', 'Moçâmedes', 'Tômbua', 'Virei',
            ],
            'Uíge' => [
                'Ambuíla', 'Bembe', 'Buengas', 'Bungo', 'Cangola',
                'Damba', 'Milunga', 'Mucaba', 'Negage', 'Puri',
                'Quimbele', 'Quitexe', 'Sanza Pombo', 'Songo', 'Uíge', 'Zombo',
            ],
            'Zaire' => [
                'Cuimba', "M'Banza Kongo", 'Nóqui', "N'Zeto", 'Soyo', 'Tomboco',
            ],
        ];

        foreach ($dados as $nomeProvincia => $municipios) {
            $provincia = Provincia::where('nome', $nomeProvincia)->first();

            if (!$provincia) {
                $this->command->warn("Província não encontrada: {$nomeProvincia}");
                continue;
            }

            foreach ($municipios as $nomeMunicipio) {
                Municipio::firstOrCreate([
                    'provincia_id' => $provincia->id,
                    'nome'         => $nomeMunicipio,
                ]);
            }
        }
    }
}
