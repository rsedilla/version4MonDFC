<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cell_group_id');
            $table->unsignedBigInteger('attendee_id');
            $table->string('attendee_type');
            $table->year('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedTinyInteger('week_number'); // 1-5
            $table->boolean('present')->default(false);
            $table->timestamps();

            $table->foreign('cell_group_id')->references('id')->on('cell_groups')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('attendance_records');
    }
};
