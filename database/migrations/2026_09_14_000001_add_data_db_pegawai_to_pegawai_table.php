<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('status_pegawai')->nullable()->after('status_kepegawaian');
            $table->string('kelamin')->nullable()->after('jenis_kelamin');
            $table->string('kelompok_besar')->nullable()->after('kelamin');
            $table->string('direktorat')->nullable()->after('kelompok_besar');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['status_pegawai', 'kelamin', 'kelompok_besar', 'direktorat']);
        });
    }
};
