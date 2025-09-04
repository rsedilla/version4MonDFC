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
        Schema::create('cell_group_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cell_group_id')->constrained('cell_groups')->onDelete('cascade');
            $table->unsignedBigInteger('attendee_id');
            $table->string('attendee_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_group_attendees');
    }
};
