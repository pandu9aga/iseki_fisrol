<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temuans', function (Blueprint $table) {
            $table->string('nik_penemu', 50)->nullable()->after('Id_Member');
            $table->string('pic_proses_nik', 50)->nullable()->after('Status_Temuan');
        });
    }

    public function down(): void
    {
        Schema::table('temuans', function (Blueprint $table) {
            $table->dropColumn(['nik_penemu', 'pic_proses_nik']);
        });
    }
};
