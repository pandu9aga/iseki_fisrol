<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_patrols', function (Blueprint $table) {
            $table->integer('Id_Daily_Patrol', true);
            $table->string('Name_Daily_Patrol', 255);
            $table->date('Time_Daily_Patrol');
        });

        Schema::create('daily_patrol_members', function (Blueprint $table) {
            $table->integer('Id_Daily_Patrol_Member', true);
            $table->integer('Id_Daily_Patrol');
            $table->integer('Id_User');
            $table->integer('Id_Member');
        });

        Schema::create('daily_temuans', function (Blueprint $table) {
            $table->integer('Id_Daily_Temuan', true);
            $table->string('Path_Daily_Temuan', 255)->nullable();
            $table->integer('Rotate_Daily_Temuan')->default(0);
            $table->text('Desc_Daily_Temuan')->nullable();
            $table->string('Path_Daily_Update_Temuan', 255)->nullable();
            $table->integer('Rotate_Daily_Update')->default(0);
            $table->text('Desc_Daily_Update_Temuan')->nullable();
            $table->integer('Id_Daily_Patrol');
            $table->integer('Id_User');
            $table->string('Status_Daily_Temuan', 50)->default('Pending');
            $table->string('pic_proses_nik', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_temuans');
        Schema::dropIfExists('daily_patrol_members');
        Schema::dropIfExists('daily_patrols');
    }
};
