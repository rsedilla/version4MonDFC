<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\G12Leader;

class G12LeadersSeeder extends Seeder
{
    public function run(): void
    {
        // Add Bon Fran as a member if not exists
        $member = Member::firstOrCreate([
            'first_name' => 'Bon Ryan',
            'last_name' => 'Fran',
        ], [
            'email' => null,
            'phone_number' => null,
            'birthday' => null,
            'address' => null,
            'civil_status_id' => null,
            'sex_id' => null,
            'leader_id' => null,
            'leader_type' => null,
        ]);

        // Add Bon Fran as a G12 leader if not exists
        G12Leader::firstOrCreate([
            'member_id' => $member->id,
        ]);
    }
}
