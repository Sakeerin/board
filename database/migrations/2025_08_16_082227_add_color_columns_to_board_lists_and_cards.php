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
        Schema::table('board_lists', function (Blueprint $table) {
            $table->string('color', 9)->nullable()->after('name'); // e.g. #RRGGBB or #RRGGBBAA
        });
        Schema::table('cards', function (Blueprint $table) {
            $table->string('color', 9)->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('board_lists', function (Blueprint $table) {
            $table->dropColumn('color');
        });
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
