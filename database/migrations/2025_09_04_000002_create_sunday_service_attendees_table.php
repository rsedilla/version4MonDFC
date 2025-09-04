<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sunday_service_attendees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable(); // Optional: for multiple services
            $table->unsignedBigInteger('attendee_id');
            $table->string('attendee_type');
            $table->date('service_date');
            $table->boolean('present')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('sunday_service_attendees');
    }
};
