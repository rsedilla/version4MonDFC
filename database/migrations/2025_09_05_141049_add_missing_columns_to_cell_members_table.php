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
        Schema::table('cell_members', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('cell_members', 'member_id')) {
                $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            }
            if (!Schema::hasColumn('cell_members', 'cell_group_id')) {
                $table->foreignId('cell_group_id')->nullable()->constrained('cell_groups')->onDelete('cascade');
            }
            if (!Schema::hasColumn('cell_members', 'joined_date')) {
                $table->date('joined_date')->nullable();
            }
            if (!Schema::hasColumn('cell_members', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cell_members', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'cell_group_id', 'joined_date', 'status']);
        });
    }
};
