<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\KeuanganIndikatorService;
use App\Services\LayananIndikatorService;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DirekturController extends Controller
{
    public function index(Request $request): View
    {
        $divisions = Division::all();

        // Dashboard Eksekutif: ringkasan lintas divisi (Layanan, SDM, Keuangan) buat 1 bulan berjalan
        $awal = $request->filled('awal')
            ? Carbon::parse($request->query('awal'))
            : now()->startOfMonth();

        $akhir = $request->filled('akhir')
            ? Carbon::parse($request->query('akhir'))
            : now();

        $layanan = app(LayananIndikatorService::class);
        $sdm = app(SdmIndikatorService::class);
        $keuangan = app(KeuanganIndikatorService::class);

        // Periode sebelumnya (durasi sama, persis sebelum periode yang dipilih) — buat hitung pertumbuhan
        $panjangHari = $awal->diffInDays($akhir) + 1;
        $akhirSebelumnya = $awal->copy()->subDay();
        $awalSebelumnya = $akhirSebelumnya->copy()->subDays($panjangHari - 1);

        $hitungPertumbuhan = function (float $sekarang, float $sebelumnya): ?float {
            if ($sebelumnya == 0) {
                return null;
            }
            return round((($sekarang - $sebelumnya) / $sebelumnya) * 100, 1);
        };

        $jumlahKunjungan = $layanan->jumlahKunjungan($awal, $akhir);
        $kehadiranSdm = $sdm->persentaseKehadiran($awal, $akhir);
        $totalPendapatan = $keuangan->totalPendapatan($awal, $akhir);

        $eksekutif = [
            'jumlah_kunjungan' => $jumlahKunjungan,
            'pertumbuhan_kunjungan' => $hitungPertumbuhan(
                $jumlahKunjungan,
                $layanan->jumlahKunjungan($awalSebelumnya, $akhirSebelumnya)
            ),
            'bor' => $layanan->bor($awal, $akhir),
            'total_pegawai' => $sdm->totalPegawai(),
            'total_pendapatan' => $totalPendapatan,
            'pertumbuhan_pendapatan' => $hitungPertumbuhan(
                $totalPendapatan,
                $keuangan->totalPendapatan($awalSebelumnya, $akhirSebelumnya)
            ),
            'kunjungan_per_bulan' => $layanan->kunjunganPerBulan(6),
            'pendapatan_per_bulan' => $keuangan->trenBulanan(6),
        ];

        return view('direktur.dashboard', [
            'divisions' => $divisions,
            'eksekutif' => $eksekutif,
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }
}
