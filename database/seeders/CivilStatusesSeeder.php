<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CivilStatusesSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['single', 'married', 'widowed'];
        foreach ($statuses as $status) {
            if (!DB::table('civil_statuses')->where('name', $status)->exists()) {
                DB::table('civil_statuses')->insert([
                    'name' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
