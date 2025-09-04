<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SexesSeeder extends Seeder
{
    public function run(): void
    {
        $sexes = ['male', 'female'];
        foreach ($sexes as $sex) {
            if (!DB::table('sexes')->where('name', $sex)->exists()) {
                DB::table('sexes')->insert([
                    'name' => $sex,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
