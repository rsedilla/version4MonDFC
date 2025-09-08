<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cell_group_infos', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('cell_group_infos', 'cell_group_idnum')) {
                $table->string('cell_group_idnum')->unique()->after('cell_group_id'); // Custom human-readable ID
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cell_group_infos', function (Blueprint $table) {
            $table->dropColumn(['cell_group_idnum']);
        });
    }
};
