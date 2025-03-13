<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            'Demande Autorisation',
            "Constitution d'entreprise SARL",
            "Constitution d'entreprise SARL-S",
            'Déclaration Impôt',
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'name' => $service,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
