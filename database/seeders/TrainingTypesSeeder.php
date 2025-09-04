<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'SUYNL',
            'LIFECLASS',
            'ENCOUNTER',
            'SOL 1',
            'SOL 2',
            'SOL 3',
            'SOL GRAD',
        ];

        foreach ($types as $type) {
            DB::table('training_types')->insert([
                'name' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
