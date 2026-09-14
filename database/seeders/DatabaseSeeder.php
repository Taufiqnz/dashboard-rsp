<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
            UnitKerjaSeeder::class,
            PoliSeeder::class,
            ProfesiSeeder::class,
            DokterSeeder::class,
            PegawaiSeeder::class,
            KamarSeeder::class,
            BedSeeder::class,
            ShiftSeeder::class,
            ObatSeeder::class,
            ReferensiSeeder::class,
            KategoriPendapatanSeeder::class,
            KategoriPengeluaranSeeder::class,
            WilayahBogorSeeder::class,
            PasienSeeder::class,
            KunjunganSeeder::class,
            RawatInapSeeder::class,
            OperasiSeeder::class,
            LaboratoriumSeeder::class,
            RadiologiSeeder::class,
            ResepSeeder::class,
            ResepDetailSeeder::class,
            // Seeder di bawah ini DINONAKTIFKAN SEMENTARA (bukan dihapus) sejak PegawaiSeeder
            // pakai data pegawai ASLI (470 orang) — jangan sampai generate data aktivitas
            // (absensi/cuti/shift/pelatihan) PALSU yang nempel ke nama orang asli.
            // Aktifkan lagi kalau nanti udah ada data aktivitas asli buat pegawai ini.
            // JadwalShiftSeeder::class,
            // AbsensiSeeder::class,
            // CutiSeeder::class,
            // PelatihanSeeder::class,
            // PegawaiPelatihanSeeder::class,
            // PenilaianKerjaSeeder::class,
            PendapatanSeeder::class,
            PengeluaranSeeder::class,
            AnggaranSeeder::class,
            RealisasiAnggaranSeeder::class,
            PiutangSeeder::class,
            KlaimBpjsSeeder::class,
        ]);
    }
}
