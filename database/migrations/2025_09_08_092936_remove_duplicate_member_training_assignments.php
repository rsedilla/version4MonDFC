<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove duplicate records, keeping only the latest one for each member-training_type combination
        DB::statement("
            DELETE t1 FROM member_training_type t1
            INNER JOIN member_training_type t2
            WHERE t1.id < t2.id
            AND t1.member_id = t2.member_id
            AND t1.training_type_id = t2.training_type_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse duplicate removal
    }
};
