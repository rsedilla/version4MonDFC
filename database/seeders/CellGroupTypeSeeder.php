<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CellGroupType;

class CellGroupTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'Open Cell',
            'Discipleship Cell',
            'G12 Cell',
        ];
        foreach ($types as $type) {
            CellGroupType::firstOrCreate(['name' => $type]);
        }
    }
}
