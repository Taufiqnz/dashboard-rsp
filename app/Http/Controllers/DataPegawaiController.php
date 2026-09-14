<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataPegawaiController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $cari = $request->query('cari');

        return view('divisi.sdm.data-pegawai', [
            'division' => $division,
            'pegawai' => app(SdmIndikatorService::class)->daftarLengkapPegawai($cari),
            'cari' => $cari,
        ]);
    }

    /**
     * Data Pegawai itu data induk (snapshot pegawai aktif sekarang), bukan data per-periode
     * kayak kunjungan pasien — jadi PDF-nya tanpa filter tanggal, cukup ikut kata kunci
     * pencarian yang lagi dipakai di halaman (kalau ada).
     */
    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'sdm', 404);

        $cari = $request->query('cari');

        $pegawai = app(SdmIndikatorService::class)->daftarLengkapPegawai($cari, false);

        $pdf = Pdf::loadView('pdf.sdm.data-pegawai', [
            'pegawai' => $pegawai,
            'cari' => $cari,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-pegawai-' . now()->format('Ymd-His') . '.pdf');
    }
}
