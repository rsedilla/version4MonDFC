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
            // Sunday Service attendance tracking
            $table->date('sunday_service_1')->nullable();
            $table->date('sunday_service_2')->nullable();
            $table->date('sunday_service_3')->nullable();
            $table->date('sunday_service_4')->nullable();
            
            // Cell Group attendance tracking
            $table->date('cell_group_1')->nullable();
            $table->date('cell_group_2')->nullable();
            $table->date('cell_group_3')->nullable();
            $table->date('cell_group_4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attenders', function (Blueprint $table) {
            // Drop Sunday Service columns
            $table->dropColumn([
                'sunday_service_1',
                'sunday_service_2', 
                'sunday_service_3',
                'sunday_service_4'
            ]);
            
            // Drop Cell Group columns
            $table->dropColumn([
                'cell_group_1',
                'cell_group_2',
                'cell_group_3',
                'cell_group_4'
            ]);
        });
    }
};
