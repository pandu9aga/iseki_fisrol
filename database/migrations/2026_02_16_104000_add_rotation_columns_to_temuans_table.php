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
        Schema::table('temuans', function (Blueprint $table) {
            $table->integer('Rotate_Temuan')->default(0)->after('Path_Temuan');
            $table->integer('Rotate_Update')->default(0)->after('Path_Update_Temuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temuans', function (Blueprint $table) {
            $table->dropColumn(['Rotate_Temuan', 'Rotate_Update']);
        });
    }
};
