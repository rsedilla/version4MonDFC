<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeniorPastorsSeeder extends Seeder
{
    public function run(): void
    {
        $pastors = [
            [
                'first_name' => 'Bishop Oriel',
                'middle_name' => null,
                'last_name' => 'Ballano',
                'email' => 'oriel.ballano@example.com',
                'phone_number' => null,
                'birthday' => null,
                'address' => null,
            ],
            [
                'first_name' => 'Pastora Geraldine',
                'middle_name' => null,
                'last_name' => 'Ballano',
                'email' => 'geraldine.ballano@example.com',
                'phone_number' => null,
                'birthday' => null,
                'address' => null,
            ],
        ];

        foreach ($pastors as $pastor) {
            $existing = DB::table('members')->where('first_name', $pastor['first_name'])
                ->where('last_name', $pastor['last_name'])
                ->first();
            if ($existing) {
                $memberId = $existing->id;
            } else {
                $memberId = DB::table('members')->insertGetId(array_merge($pastor, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
            if (!DB::table('senior_pastors')->where('member_id', $memberId)->exists()) {
                DB::table('senior_pastors')->insert([
                    'member_id' => $memberId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
