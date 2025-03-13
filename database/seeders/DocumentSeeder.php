<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    public function run()
    {
        $documents = [
            'Demande Autorisation' => [
                "Pièce d'identité du demandeur",
                'Copie du contrat de bail',
                'Extrait de casier judiciaire français',
                'Extrait de casier judiciaire luxembourgeois',
                'Déclaration de non-faillite - Sur rendez-vous notaire',
                'Projet de statut',
                'Copie des diplômes',
                'RIB de la société',
            ],
            "Constitution d'entreprise SARL" => [
                "Pièce d'identité des associés",
                'ACTE DE CONSTITUTION',
                'Contrat de bail signé',
                "Déclaration sur l'honneur",
            ],
            "Constitution d'entreprise SARL-S" => [
                "Pièce d'identité des associés",
                'RIB société',
                'Contrat de bail signé',
                'Statuts (acte sous seing privé)',
                'Preuve de dépôts des statuts au LBR',
            ],
            'Déclaration Impôt' => [
                'Certificat de rémunération annuel',
                'Intérêts débiteurs',
                'Assurances',
                'Assurance prévoyance vieillesse',
                'Épargne logement',
                'Dons',
                'Charges extraordinaires',
                'Déclaration de partenariat',
                'Une copie du décompte annuel ou de la déclaration d’impôts de l’année précédente',
                'Revenus locatifs',
            ],
        ];

        foreach ($documents as $serviceName => $docs) {
            $service = DB::table('services')->where('name', $serviceName)->first();

            if ($service) {
                foreach ($docs as $doc) {
                    DB::table('documents')->insert([
                        'service_id' => $service->id,
                        'name' => $doc,
                        'type' => "folder",
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }
}
