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
        Schema::table('member_training_type', function (Blueprint $table) {
            // Add the new training_status_id column without foreign key constraint first
            $table->unsignedBigInteger('training_status_id')->nullable()->after('training_type_id');
        });
        
        // Update existing records to use 'Not Enrolled' status (id: 2)
        DB::table('member_training_type')->update(['training_status_id' => 2]);
        
        Schema::table('member_training_type', function (Blueprint $table) {
            // Now add the foreign key constraint
            $table->foreign('training_status_id')->references('id')->on('training_statuses')->onDelete('cascade');
            
            // Drop the old status enum column
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_training_type', function (Blueprint $table) {
            // Re-add the status enum column
            $table->enum('status', ['Enrolled', 'Not Enrolled', 'Ongoing'])->default('Not Enrolled')->after('training_type_id');
        });
        
        Schema::table('member_training_type', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['training_status_id']);
            $table->dropColumn('training_status_id');
        });
    }
};
