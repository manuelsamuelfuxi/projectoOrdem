<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;
use App\Models\Funcao;

class CursoFuncaoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Cursos ────────────────────────────────────────────────────────────
        $cursos = [
            'Análises Clínicas e de Saúde Pública',
            'Farmácia',
            'Fisioterapia',
            'Estomatologia',
            'Radiologia',
            'Anatomia Patológica, Citologia e Tanatologia',
            'Audiologia',
            'Cardiopneumologia',
            'Dietética',
            'Higiene Oral',
            'Medicina Nuclear',
            'Neurofisiologia',
            'Ortóptica e Oftalmologia',
            'Ortoprótesia',
            'Prótese Dentária e Odontologia',
            'Radioterapia',
            'Terapia da Fala',
            'Terapia Ocupacional',
            'Saúde Ambiental',
            'Biologia Laboratorial',
            'Defectologia',
            'Electromedicina',
            'Estatística Médica',
            'Higiene e Epidemiologia',
            'Genética',
            'Psicologia Clínica Patológica',
        ];

        foreach ($cursos as $nome) {
            Curso::firstOrCreate(['nome' => $nome]);
        }

        // ── Funções ───────────────────────────────────────────────────────────
        $funcoes = [
            'Técnico de Análises Clínicas e de Saúde Pública',
            'Técnico de Farmácia',
            'Fisioterapeuta',
            'Estomatologista',
            'Técnico de Radiologia',
            'Técnico de Anatomia Patológica, Citologia e Tanatologia',
            'Técnico de Audiologia',
            'Técnico de Cardiopneumologia',
            'Dietista',
            'Higienista Oral',
            'Técnico de Medicina Nuclear',
            'Técnico de Neurofisiologia',
            'Ortoptista / Oftalmologista',
            'Ortoprotésico',
            'Técnico de Prótese Dentária / Odontologista',
            'Técnico de Radioterapia',
            'Terapeuta da Fala',
            'Terapeuta Ocupacional',
            'Técnico de Saúde Ambiental',
            'Biólogo Laboratorial',
            'Defectologista',
            'Técnico de Electromedicina',
            'Estatístico Médico',
            'Técnico de Higiene e Epidemiologia',
            'Geneticista',
            'Psicólogo Clínico Patológico',
            'Outra',
        ];

        foreach ($funcoes as $nome) {
            Funcao::firstOrCreate(['nome' => $nome]);
        }
    }
}
