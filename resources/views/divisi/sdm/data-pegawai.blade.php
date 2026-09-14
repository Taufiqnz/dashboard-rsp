@extends('layouts.dashboard')

@section('title', 'Data Pegawai')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; white-space: nowrap; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 5px 12px; font-weight: 600; font-size: 11px; }
    .badge-kosong {
        border-radius: 20px; padding: 4px 10px; font-weight: 600; font-size: 10.5px;
        background: #F3F6F9; color: #a1a5b7; font-style: italic;
    }
    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #f1f1f4;
    }
    .table-footer-summary { color: #7e8299; font-size: 12px; white-space: nowrap; }
    .table-footer nav { max-width: 100%; overflow-x: auto; padding-bottom: 3px; }
    .table-footer .pagination {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        margin: 0;
    }
    .table-footer .pagination .page-item { margin: 0; }
    .table-footer .pagination .page-link {
        min-width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border: 1px solid #e4e7ec;
        border-radius: 8px;
        color: #7e8299;
        background: #fff;
        font-size: 12px;
        line-height: 1;
    }
    .table-footer .pagination .page-item.active .page-link {
        color: #fff;
        border-color: #8950fc;
        background: #8950fc;
    }
    .table-footer .pagination .page-item.disabled .page-link {
        color: #c4c7d5;
        background: #f8f9fb;
    }
    .table-footer .pagination .page-link:hover {
        color: #6236db;
        border-color: #c9bcff;
        background: #faf8ff;
    }
    @media (max-width: 767.98px) {
        .table-footer { align-items: stretch; flex-direction: column; gap: 12px; }
        .table-footer-summary { white-space: normal; }
        .table-footer nav { width: 100%; overflow-x: visible; }
        .table-footer .pagination { justify-content: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')

        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-id-card mr-2"></i>Data Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Data induk kepegawaian lengkap, {{ $pegawai->total() }} pegawai aktif</span>
        </div>
        <a href="{{ route('divisi.sdm.data-pegawai.pdf', array_filter(['division' => $division->slug, 'cari' => $cari])) }}"
           target="_blank" class="btn btn-dark font-weight-bold">
            Download PDF
        </a>
    </div>

    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
        <div class="form-group mb-0 mr-3">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari Nama/NIP/NIK</label>
            <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari..." class="form-control form-control-solid" style="width: 250px;">
        </div>
        <button type="submit" class="btn btn-primary font-weight-bold px-6"><i class="fas fa-search mr-2"></i>Cari</button>
        @if ($cari)
            <a href="{{ route('divisi.sdm.data-pegawai', $division->slug) }}" class="ml-3 text-muted font-weight-bold">Reset</a>
        @endif
    </form>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            {{-- <div class="d-flex align-items-center p-3 mb-4" style="background:#F9F9FB; border-radius:10px;">
                {{-- <i class="fas fa-circle-info fa-info-circle text-muted mr-3"></i> --}}
                {{-- <span class="text-muted font-size-sm">
                    Kolom bertanda <span class="badge-kosong">Belum diisi</span> berarti datanya belum tersedia dari sumber data asli —
                    bisa dilengkapi manual kapan saja.
                </span> --}}
            {{-- </div> --}} 
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Status Pegawai</th>
                            <th>NIP</th>
                            <th>Kelamin</th>
                            <th>Kelompok Besar</th>
                            <th>Golongan</th>
                            <th>Direktorat</th>
                            <th>Unit Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawai as $p)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $p->nama }}</td>
                            <td>
                                @if ($p->status_pegawai)
                                    {{ $p->status_pegawai }}
                                @else
                                    <span class="badge-kosong">Belum diisi</span>
                                @endif
                            </td>
                            <td class="font-weight-bold text-dark">{{ $p->nip }}</td>
                            <td>
                                <span class="badge badge-modern" style="background:{{ $p->kelamin === 'Laki-Laki' ? '#EEF3FF' : '#FFE9EA' }}; color:{{ $p->kelamin === 'Laki-Laki' ? '#6993FF' : '#F64E60' }};">
                                    {{ $p->kelamin ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($p->kelompok_besar)
                                    {{ $p->kelompok_besar }}
                                @else
                                    <span class="badge-kosong">Belum diisi</span>
                                @endif
                            </td>
                            <td>
                                @if ($p->golongan)
                                    {{ $p->golongan }}
                                @else
                                    <span class="badge-kosong">Belum diisi</span>
                                @endif
                            </td>
                            <td>{{ $p->direktorat ?? '-' }}</td>
                            <td>{{ $p->unitKerja?->nama_unit ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">Tidak ada pegawai yang cocok dengan pencarian</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pegawai->hasPages())
                <div class="table-footer">
                    <span class="table-footer-summary">
                        Menampilkan {{ $pegawai->firstItem() }}-{{ $pegawai->lastItem() }} dari {{ $pegawai->total() }} pegawai
                    </span>
                    <nav aria-label="Navigasi halaman data pegawai">
                        <ul class="pagination">
                            <li class="page-item {{ $pegawai->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $pegawai->previousPageUrl() ?? '#' }}" aria-label="Halaman sebelumnya">&lsaquo;</a>
                            </li>
                            @for ($page = 1; $page <= $pegawai->lastPage(); $page++)
                                <li class="page-item {{ $pegawai->currentPage() === $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $pegawai->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $pegawai->currentPage() === $pegawai->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $pegawai->nextPageUrl() ?? '#' }}" aria-label="Halaman berikutnya">&rsaquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection