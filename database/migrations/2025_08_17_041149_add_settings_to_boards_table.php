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
        Schema::table('boards', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('owner_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('priority', 32)->nullable()->after('end_date'); // general|medium|urgent|very_urgent
            $table->string('color', 9)->nullable()->after('priority');     // #RRGGBB or #RRGGBBAA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boards', function (Blueprint $table) {
             $table->dropColumn(['start_date','end_date','priority','color']);
        });
    }
};
