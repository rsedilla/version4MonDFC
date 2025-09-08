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
        Schema::create('training_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert default status values
        DB::table('training_statuses')->insert([
            ['name' => 'Enrolled', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Not Enrolled', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Graduate', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_statuses');
    }
};
