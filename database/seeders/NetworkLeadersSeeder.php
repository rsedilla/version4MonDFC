<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworkLeadersSeeder extends Seeder
{
    public function run(): void
    {
        $leaders = [
            'Albert Castro',
            'Daniel Oriel Ballano',
            'Darwin Dumael',
            'David Herald Felicelda',
            'Jeffrey Nel Figueroa',
            'John Benz Samson',
            'John Isaac Lausin',
            'Michael Roque',
            'Karl Nicholas Lisondra',
            'Raymond Sedilla',
            'Romeo Malificiar Jr',
            'Virgilio Abogado',
            'Ana Camille Polandaya',
            'Diane Grace Malificiar',
            'Dineriel Grace Felicelda',
            'Divina Ranay',
            'Eden Abogado',
            'Emelda Dalina',
            'Florie Ann Juliano',
            'Geraldine Ballano',
            'Joy Delen',
            'Jumelyn Torres',
            'Lilibeth Dorado',
            'Mary Grace Calilong',
            'Ranee Nicole Sedilla',
            'Rudgie Marie Teodocio',
            'Sierra Lee Manalo',
            'Victoria Roque',
        ];

        foreach ($leaders as $leader) {
            $parts = explode(' ', $leader, 3);
            $first_name = $parts[0] ?? null;
            $middle_name = isset($parts[2]) ? $parts[1] : null;
            $last_name = isset($parts[2]) ? $parts[2] : ($parts[1] ?? null);

            $memberId = DB::table('members')->insertGetId([
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'email' => null,
                'phone_number' => null,
                'birthday' => null,
                'civil_status_id' => null,
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('network_leaders')->insert([
                'member_id' => $memberId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
