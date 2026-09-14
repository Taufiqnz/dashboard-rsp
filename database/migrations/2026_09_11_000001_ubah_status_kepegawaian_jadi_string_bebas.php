<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Data asli pegawai punya 5 status: PNS, PPPK, BLU, Mitra, Magang.
        // Diganti dari enum kaku jadi string bebas biar nggak perlu migration lagi
        // kalau suatu saat ada kategori status baru.
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_kepegawaian VARCHAR(30) NOT NULL DEFAULT 'pns'");

        // Tanggal masuk cuma bisa dipastikan buat pegawai berstatus PNS (bisa diekstrak dari
        // format NIP standar). Buat PPPK/BLU/Mitra/Magang formatnya beda-beda dan nggak bisa
        // dipastikan, jadi kolom ini perlu jadi nullable daripada dipaksa isi tanggal ngasal.
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN tanggal_masuk DATE NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_kepegawaian ENUM('pns', 'pppk', 'kontrak_blu') NOT NULL DEFAULT 'kontrak_blu'");
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN tanggal_masuk DATE NOT NULL");
    }
};