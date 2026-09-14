<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\PegawaiPelatihan;
use Illuminate\Support\Carbon;

class SdmIndikatorService
{
    /**
     * Total pegawai aktif.
     */
    public function totalPegawai(): int
    {
        return Pegawai::where('aktif', true)->count();
    }

    /**
     * Jumlah pegawai per status kepegawaian: PNS, PPPK, Kontrak BLU.
     * Dipakai buat kartu ringkasan di halaman utama Dashboard SDM.
     */
    public function jumlahPerStatusKepegawaian(): array
    {
        $hasil = Pegawai::where('aktif', true)
            ->selectRaw('status_kepegawaian, count(*) as total')
            ->groupBy('status_kepegawaian')
            ->pluck('total', 'status_kepegawaian');

        return [
            'pns' => (int) ($hasil['pns'] ?? 0),
            'pppk' => (int) ($hasil['pppk'] ?? 0),
            'blu' => (int) ($hasil['blu'] ?? 0),
            'mitra' => (int) ($hasil['mitra'] ?? 0),
            'magang' => (int) ($hasil['magang'] ?? 0),
        ];
    }

    /**
     * Komposisi SDM berdasarkan kelompok besar dari data asli db_pegawai.
     * Return: collection [ ['kelompok' => ..., 'label' => ..., 'total' => ..., 'persentase' => ...], ... ]
     * Diurutkan dari yang jumlahnya paling besar.
     */
    public function komposisiSdm()
    {
        $total = $this->totalPegawai();

        if ($total === 0) {
            return collect();
        }

        $kelompokTerhitung = Pegawai::where('aktif', true)
            ->pluck('kelompok_besar')
            ->map(fn($kelompok) => $kelompok ?: 'Belum Diisi')
            ->countBy();

        return $kelompokTerhitung
            ->map(fn($jumlah, $kelompok) => [
                'kelompok' => $kelompok,
                'label' => $kelompok,
                'total' => $jumlah,
                'persentase' => round(($jumlah / $total) * 100, 1),
            ])
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Persentase kehadiran pegawai dalam periode.
     * Rumus: jumlah hadir / jumlah hari kerja wajib x 100%
     * "Hadir" di sini mencakup status hadir & terlambat (keduanya tetap masuk kerja).
     */
    public function persentaseKehadiran(Carbon $awal, Carbon $akhir): float
    {
        $totalAbsensi = Absensi::whereBetween('tanggal', [$awal, $akhir])->count();

        if ($totalAbsensi === 0) {
            return 0;
        }

        $hadir = Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        return round(($hadir / $totalAbsensi) * 100, 1);
    }

    /**
     * Rekap kehadiran per status (hadir, terlambat, izin, sakit, alpha) dalam periode.
     */
    public function rekapStatusAbsensi(Carbon $awal, Carbon $akhir)
    {
        return Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
    }

    /**
     * Jumlah pegawai yang cuti/izin dalam rentang tanggal filter (bukan cuma snapshot hari ini).
     * Gabungan dari:
     * - Cuti yang disetujui & jadwalnya overlap dengan periode filter (tabel Cuti)
     * - Record absensi berstatus 'izin' dalam periode filter (tabel Absensi)
     */
    public function jumlahCutiAktif(Carbon $awal, Carbon $akhir): int
    {
        $jumlahCuti = \App\Models\Cuti::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $akhir)
            ->whereDate('tanggal_selesai', '>=', $awal)
            ->count();

        $jumlahIzin = Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->where('status', 'izin')
            ->count();

        return $jumlahCuti + $jumlahIzin;
    }

    /**
     * Distribusi pegawai per unit kerja.
     */
    public function distribusiPerUnit()
    {
        return Pegawai::where('aktif', true)
            ->join('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->selectRaw('unit_kerja.nama_unit, count(*) as total')
            ->groupBy('unit_kerja.nama_unit')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Jumlah pegawai yang mengikuti pelatihan dalam periode (berdasarkan tanggal pelatihan).
     */
    public function jumlahIkutPelatihan(Carbon $awal, Carbon $akhir): int
    {
        return PegawaiPelatihan::whereIn('status', ['selesai', 'mengikuti'])
            ->whereHas('pelatihan', function ($q) use ($awal, $akhir) {
                $q->whereBetween('tanggal_mulai', [$awal, $akhir]);
            })
            ->count();
    }

    /**
     * Target kehadiran yang ditetapkan rumah sakit (persen). Dipakai buat bandingkan
     * dengan realisasi kehadiran tiap bulan.
     */
    protected function targetKehadiran(): float
    {
        return 95.0;
    }

    /**
     * Realisasi kehadiran per bulan (N bulan terakhir) dibandingkan dengan target,
     * plus status: "Baik" (di atas target), "Sesuai target" (pas target), atau
     * "Perlu perhatian" (di bawah target). Dipakai buat tabel indikator kehadiran bulanan.
     */
    public function kehadiranBulanan(int $jumlahBulan = 5)
    {
        $target = $this->targetKehadiran();
        $hasil = collect();

        for ($i = $jumlahBulan - 1; $i >= 0; $i--) {
            $bulanAcuan = now()->subMonths($i);
            $awalBulan = $bulanAcuan->copy()->startOfMonth();
            $akhirBulan = $bulanAcuan->copy()->endOfMonth()->min(now());

            $realisasi = $this->persentaseKehadiran($awalBulan, $akhirBulan);

            $status = match (true) {
                $realisasi > $target => 'Baik',
                $realisasi < $target => 'Perlu perhatian',
                default => 'Sesuai target',
            };

            $hasil->push([
                'bulan' => $bulanAcuan->translatedFormat('F Y'),
                'target' => $target,
                'realisasi' => $realisasi,
                'status' => $status,
            ]);
        }

        return $hasil;
    }

    /**
     * Daftar record absensi dalam periode (dipakai di sub-menu "Kehadiran"), dengan info pegawai.
     */
    public function daftarAbsensi(Carbon $awal, Carbon $akhir, ?string $status = null)
    {
        $query = Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->with('pegawai')
            ->orderByDesc('tanggal');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Daftar pengajuan cuti dalam periode (dipakai di sub-menu "Cuti & Izin"), dengan info pegawai.
     */
    public function daftarCuti(Carbon $awal, Carbon $akhir, ?string $status = null)
    {
        $query = \App\Models\Cuti::where(function ($q) use ($awal, $akhir) {
            $q->whereDate('tanggal_mulai', '<=', $akhir)
                ->whereDate('tanggal_selesai', '>=', $awal);
        })
            ->with('pegawai')
            ->orderByDesc('tanggal_mulai');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Daftar record izin harian (bukan cuti terjadwal) dalam periode, dari tabel absensi.
     */
    public function daftarIzinHarian(Carbon $awal, Carbon $akhir)
    {
        return Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->where('status', 'izin')
            ->with('pegawai')
            ->orderByDesc('tanggal')
            ->get();
    }

    /**
     * Daftar pelatihan dalam periode berikut jumlah pesertanya (dipakai di sub-menu "Pelatihan").
     */
    public function daftarPelatihan(Carbon $awal, Carbon $akhir)
    {
        return \App\Models\Pelatihan::whereBetween('tanggal_mulai', [$awal, $akhir])
            ->withCount([
                'peserta as jumlah_peserta' => fn($q) => $q->whereIn('status', ['selesai', 'mengikuti']),
                'peserta as jumlah_selesai' => fn($q) => $q->where('status', 'selesai'),
            ])
            ->orderByDesc('tanggal_mulai')
            ->get();
    }

    /**
     * Distribusi pegawai per unit kerja, LENGKAP dengan daftar nama pegawainya masing-masing
     * (dipakai di sub-menu "Distribusi Pegawai"). Beda dengan distribusiPerUnit() yang cuma
     * hitungan ringkas buat kartu di halaman Ringkasan.
     */
    public function distribusiPerUnitDetail()
    {
        return Pegawai::where('aktif', true)
            ->with(['profesi', 'unitKerja'])
            ->get()
            ->groupBy(fn($p) => $p->unitKerja->nama_unit ?? 'Tanpa Unit')
            ->map(fn($group, $namaUnit) => [
                'nama_unit' => $namaUnit,
                'total' => $group->count(),
                'pegawai' => $group->sortBy('nama')->values(),
            ])
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Tabel 1: Data Pegawai ringkas. Dipakai di sub-menu "Data Pegawai".
     */
    public function daftarLengkapPegawai(?string $cari = null, bool $paginate = true)
    {
        $query = Pegawai::where('aktif', true)->with(['unitKerja']);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nip', 'like', "%{$cari}%")
                    ->orWhere('nik', 'like', "%{$cari}%");
            });
        }

        $query->orderBy('nama');

        return $paginate ? $query->paginate(50)->withQueryString() : $query->get();
    }

    /**
     * Jam kerja standar buat pegawai non-shift (staf administrasi, dsb).
     */
    protected function jamKerjaNonShift(): array
    {
        return ['jam_masuk' => '08:00', 'jam_keluar' => '16:00'];
    }

    /**
     * Tabel 2: Jadwal Kerja per tanggal (NIP, Jam Masuk, Jam Keluar, Unit Kerja, Jenis Shift).
     * Pegawai non-shift (staf tetap/administrasi) selalu tampil dengan jam kerja standar dan
     * label "Non-Shift". Pegawai shift ikut jadwal_shift pada tanggal terpilih — kalau nggak
     * ada jadwal di tanggal itu (libur), ditandai "Libur".
     */
    public function jadwalKerja(Carbon $tanggal)
    {
        $jamNonShift = $this->jamKerjaNonShift();

        $pegawaiList = Pegawai::where('aktif', true)
            ->with(['unitKerja', 'jadwalShift' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)->with('shift');
            }])
            ->orderBy('nama')
            ->get();

        return $pegawaiList->map(function ($p) use ($jamNonShift) {
            if ($p->jenis_kerja === 'non_shift') {
                return [
                    'nip' => $p->nip,
                    'nama' => $p->nama,
                    'unit_kerja' => $p->unitKerja->nama_unit ?? '-',
                    'jenis_shift' => 'Non-Shift',
                    'jam_masuk' => $jamNonShift['jam_masuk'],
                    'jam_keluar' => $jamNonShift['jam_keluar'],
                ];
            }

            $jadwalHariIni = $p->jadwalShift->first();

            return [
                'nip' => $p->nip,
                'nama' => $p->nama,
                'unit_kerja' => $p->unitKerja->nama_unit ?? '-',
                'jenis_shift' => $jadwalHariIni->shift->nama_shift ?? 'Libur',
                'jam_masuk' => $jadwalHariIni->shift->jam_mulai ?? '-',
                'jam_keluar' => $jadwalHariIni->shift->jam_selesai ?? '-',
            ];
        });
    }

    public function produktivitasPerUnit(Carbon $awal, Carbon $akhir)
    {
        $pegawaiPerUnit = Pegawai::where('pegawai.aktif', true)
            ->join('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->selectRaw('unit_kerja.id as unit_kerja_id, unit_kerja.nama_unit, count(*) as jumlah_pegawai')
            ->groupBy('unit_kerja.id', 'unit_kerja.nama_unit')
            ->get();

        $kunjunganPerUnit = \App\Models\Kunjungan::whereBetween('kunjungan.waktu_daftar', [$awal, $akhir])
            ->join('poli', 'kunjungan.poli_id', '=', 'poli.id')
            ->selectRaw('poli.unit_kerja_id, count(*) as beban_kerja')
            ->groupBy('poli.unit_kerja_id')
            ->get()
            ->pluck('beban_kerja', 'unit_kerja_id');

        return $pegawaiPerUnit->map(function ($unit) use ($kunjunganPerUnit) {
            $bebanKerja = (int) ($kunjunganPerUnit[$unit->unit_kerja_id] ?? 0);
            $rasio = $unit->jumlah_pegawai > 0
                ? round($bebanKerja / $unit->jumlah_pegawai, 1)
                : 0;

            return [
                'nama_unit' => $unit->nama_unit,
                'jumlah_pegawai' => $unit->jumlah_pegawai,
                'beban_kerja' => $bebanKerja,
                'rasio' => $rasio,
            ];
        })->sortByDesc('rasio')->values();
    }

    /**
     * Ambil semua indikator sekaligus — dipanggil dari Controller.
     */
    public function ringkasan(Carbon $awal, Carbon $akhir): array
    {
        return [
            'total_pegawai' => $this->totalPegawai(),
            'status_kepegawaian' => $this->jumlahPerStatusKepegawaian(),
            'komposisi_sdm' => $this->komposisiSdm(),
            'persentase_kehadiran' => $this->persentaseKehadiran($awal, $akhir),
            'rekap_status_absensi' => $this->rekapStatusAbsensi($awal, $akhir),
            'jumlah_cuti_aktif' => $this->jumlahCutiAktif($awal, $akhir),
            'distribusi_per_unit' => $this->distribusiPerUnit(),
            'jumlah_ikut_pelatihan' => $this->jumlahIkutPelatihan($awal, $akhir),
            'kehadiran_bulanan' => $this->kehadiranBulanan(5),
        ];
    }
}
