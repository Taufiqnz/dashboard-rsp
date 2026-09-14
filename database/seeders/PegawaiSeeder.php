<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Pegawai;
use App\Models\Profesi;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $divisiSdm = Division::where('slug', 'sdm')->firstOrFail();

        // Peta isi kolom "golongan" di data asli (sebenernya sub-profesi, BUKAN pangkat)
        // ke nama_profesi yang udah ada di tabel profesi.
        $petaProfesi = [
            'Dokter Umum' => 'Dokter Umum',
            'Dokter Spesialis' => 'Dokter Spesialis',
            'Dokter Sub Spesialis' => 'Dokter Sub Spesialis',
            'Dokter Gigi' => 'Dokter Gigi',
            'Perawat' => 'Perawat',
            'Bidan' => 'Bidan',
            'Nakes Lain' => 'Nakes Lain',
            'Administrasi' => 'Administrasi',
        ];

        $profesiCache = Profesi::whereIn('nama_profesi', array_values($petaProfesi))
            ->get()->keyBy('nama_profesi');

        $unitCache = UnitKerja::where('division_id', $divisiSdm->id)->get()->keyBy('nama_unit');

        // Query LANGSUNG ke database db_pegawai (database terpisah, tabel bernama sama "pegawai").
        // Server MySQL-nya harus sama & user DB di .env harus punya akses ke db_pegawai ini.
        try {
            $dataAsli = DB::table('db_pegawai.pegawai')->get();
        } catch (\Throwable $e) {
            $this->command?->error(
                'Gagal konek ke database db_pegawai: ' . $e->getMessage() . PHP_EOL .
                    'Pastikan: (1) database "db_pegawai" ada di server MySQL yang sama, ' .
                    '(2) user database di .env (DB_USERNAME) punya akses ke db_pegawai itu.'
            );
            return;
        }

        if ($dataAsli->isEmpty()) {
            $this->command?->warn('Tabel db_pegawai.pegawai kosong, nggak ada yang di-import.');
            return;
        }

        foreach ($dataAsli as $row) {
            $namaUnit = $row->unit_kerja !== null && $row->unit_kerja !== ''
                ? $row->unit_kerja
                : 'Belum Ditentukan';

            if (!isset($unitCache[$namaUnit])) {
                $kodeUnit = 'SDM-' . strtoupper(Str::slug($namaUnit));
                $kodeUnit = substr($kodeUnit, 0, 50);

                $unitCache[$namaUnit] = UnitKerja::firstOrCreate(
                    ['kode_unit' => $kodeUnit],
                    ['nama_unit' => $namaUnit, 'division_id' => $divisiSdm->id]
                );
            }

            $namaProfesi = $petaProfesi[$row->golongan] ?? 'Administrasi';
            $profesi = $profesiCache[$namaProfesi] ?? $profesiCache['Administrasi'];

            // Samain penulisan status jadi lowercase-underscore, konsisten sama kode lain
            $status = match ($row->status_pegawai) {
                'PNS' => 'pns',
                'PPPK' => 'pppk',
                'BLU' => 'blu',
                'Mitra' => 'mitra',
                'Magang' => 'magang',
                default => 'mitra',
            };

            // Tenaga klinis (dokter/perawat/bidan/nakes lain) kerja shift, administrasi jam tetap
            $jenisKerja = $row->kelompok_besar === 'Administrasi' ? 'non_shift' : 'shift';

            [$tanggalLahir, $tanggalMasuk] = $this->ekstrakTanggalDariNip($row->nip, $status);

            Pegawai::updateOrCreate(
                ['nip' => $row->nip],
                [
                    'nama' => $row->nama,
                    'profesi_id' => $profesi->id,
                    'unit_kerja_id' => $unitCache[$namaUnit]->id,
                    'jenis_kelamin' => $row->kelamin === 'Laki-Laki' ? 'L' : 'P',
                    'kelamin' => $row->kelamin,
                    'kelompok_besar' => $row->kelompok_besar,
                    'direktorat' => $row->direktorat,
                    'tanggal_lahir' => $tanggalLahir,
                    'tanggal_masuk' => $tanggalMasuk,
                    'status_kepegawaian' => $status,
                    'status_pegawai' => $row->status_pegawai,
                    // field ini nggak ada di data asli, dibiarkan kosong dulu
                    'pendidikan' => null,
                    'jabatan' => null,
                    'golongan' => $row->golongan,
                    'jenis_kerja' => $jenisKerja,
                    'no_hp' => null,
                    'aktif' => true,
                ]
            );
        }

        $this->command?->info('Berhasil import ' . $dataAsli->count() . ' pegawai dari db_pegawai.pegawai.');
    }

    /**
     * NIP PNS di Indonesia formatnya baku: 8 digit tanggal lahir (YYYYMMDD) + 6 digit
     * tahun-bulan TMT (YYYYMM) + 1 digit kelamin + 3 digit nomor urut = 18 digit.
     * Ini kita manfaatin buat "ekstrak" tanggal lahir & tanggal masuk kerja pegawai PNS
     * TANPA perlu data tambahan.
     *
     * Untuk PPPK/BLU/Mitra/Magang, formatnya beda-beda dan nggak bisa dipastikan artinya
     * apa — jadi sengaja dibiarkan null daripada nebak-nebak data yang bisa salah.
     */
    private function ekstrakTanggalDariNip(string $nip, string $status): array
    {
        if ($status !== 'pns' || strlen($nip) !== 18) {
            return [null, null];
        }

        $tahunLahir = (int) substr($nip, 0, 4);
        $bulanLahir = (int) substr($nip, 4, 2);
        $tanggalLahirAngka = (int) substr($nip, 6, 2);
        $tahunMasuk = (int) substr($nip, 8, 4);
        $bulanMasuk = (int) substr($nip, 12, 2);

        // validasi rentang wajar, kalau nggak masuk akal jangan dipaksa
        if ($tahunLahir < 1930 || $tahunLahir > 2010 || $bulanLahir < 1 || $bulanLahir > 12) {
            return [null, null];
        }
        if ($tahunMasuk < 1970 || $tahunMasuk > 2026 || $bulanMasuk < 1 || $bulanMasuk > 12) {
            return [null, null];
        }

        $tanggalLahirFinal = $tanggalLahirAngka > 0 ? $tanggalLahirAngka : 1;

        try {
            $tanggalLahir = sprintf('%04d-%02d-%02d', $tahunLahir, $bulanLahir, $tanggalLahirFinal);
            $tanggalMasuk = sprintf('%04d-%02d-01', $tahunMasuk, $bulanMasuk);

            return [$tanggalLahir, $tanggalMasuk];
        } catch (\Throwable $e) {
            return [null, null];
        }
    }
}
