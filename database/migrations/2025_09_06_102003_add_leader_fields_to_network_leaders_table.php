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
        Schema::table('network_leaders', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->nullable()->after('user_id');
            $table->string('leader_type')->nullable()->after('leader_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_leaders', function (Blueprint $table) {
            $table->dropColumn(['leader_id', 'leader_type']);
        });
    }
};
