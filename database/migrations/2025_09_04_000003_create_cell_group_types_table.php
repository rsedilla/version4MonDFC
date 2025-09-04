<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('cell_group_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        // Add foreign key to cell_groups table
        Schema::table('cell_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('cell_group_type_id')->nullable()->after('id');
            $table->foreign('cell_group_type_id')->references('id')->on('cell_group_types')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('cell_groups', function (Blueprint $table) {
            $table->dropForeign(['cell_group_type_id']);
            $table->dropColumn('cell_group_type_id');
        });
        Schema::dropIfExists('cell_group_types');
    }
};
