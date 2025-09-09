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
        Schema::table('attenders', function (Blueprint $table) {
            $table->unsignedBigInteger('consolidator_id')->nullable()->after('member_id');
            $table->foreign('consolidator_id')->references('id')->on('members')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attenders', function (Blueprint $table) {
            $table->dropForeign(['consolidator_id']);
            $table->dropColumn('consolidator_id');
        });
    }
};
