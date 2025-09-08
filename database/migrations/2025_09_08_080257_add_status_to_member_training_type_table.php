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
            $table->enum('status', ['Enrolled', 'Not Enrolled', 'Ongoing'])->default('Not Enrolled')->after('training_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_training_type', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
