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
        Schema::table('member_training_type', function (Blueprint $table) {
            $table->unique(['member_id', 'training_type_id'], 'unique_member_training_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_training_type', function (Blueprint $table) {
            $table->dropUnique('unique_member_training_type');
        });
    }
};
