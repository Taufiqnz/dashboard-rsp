@extends('layouts.dashboard')

@section('title', 'Dashboard Direktur')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }

    .page-header {
        background: linear-gradient(135deg, #17A2B8 0%, #0E6B7A 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(23,162,184,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.85) !important; }

    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }

    /* Kartu KPI gaya "Contoh Tampilan Dashboard Eksekutif" — border tosca, isi rata tengah */
    .kpi-card {
        background: #fff;
        border: 1.5px solid #B7EAD6;
        border-radius: 16px;
        padding: 22px 18px;
        text-align: center;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.06); }
    .kpi-title { font-size: 13.5px; font-weight: 600; color: #6c7a89; margin-bottom: 8px; }
    .kpi-value { font-size: 28px; font-weight: 800; color: #1B4F72; line-height: 1.1; }
    .kpi-sub { font-size: 12.5px; font-weight: 600; margin-top: 8px; }
    .kpi-sub.up { color: #1BC5BD; }
    .kpi-sub.down { color: #F64E60; }
    .kpi-sub.neutral { color: #a1a5b7; }

    .kpi-link {
        display: inline-flex; align-items: center; gap: 4px;
        margin-top: 12px; font-size: 12.5px; font-weight: 700;
        color: #17A2B8; text-decoration: none;
    }
    .kpi-link:hover { color: #0E6B7A; text-decoration: none; }
    .kpi-link i { font-size: 10px; transition: transform .15s ease; }
    .kpi-link:hover i { transform: translateX(3px); }

    .chart-title { font-weight: 700; font-size: 16px; color: #181c32; text-align: center; margin-bottom: 18px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-chart-pie mr-2"></i>Dashboard Eksekutif</h1>
            <span class="text-muted-light font-weight-bold">
                Login sebagai <strong>{{ auth()->user()->name }}</strong> &middot; Ringkasan lintas divisi RSP Goenawan Cisarua
            </span>
        </div>
    </div>

    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
            <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 170px;">
        </div>
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
            <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 170px;">
        </div>
        <button type="submit" class="btn btn-info font-weight-bold px-6 text-white"><i class="fas fa-filter mr-2"></i>Terapkan</button>
    </form>

    {{-- 4 Kartu KPI Eksekutif --}}
    <div class="row mb-6">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card">
                <div class="kpi-title">Kunjungan</div>
                <div class="kpi-value">{{ number_format($eksekutif['jumlah_kunjungan'], 0, ',', '.') }}</div>
                @if (is_null($eksekutif['pertumbuhan_kunjungan']))
                    <div class="kpi-sub neutral">Periode sebelumnya kosong</div>
                @else
                    <div class="kpi-sub {{ $eksekutif['pertumbuhan_kunjungan'] >= 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $eksekutif['pertumbuhan_kunjungan'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                        {{ abs($eksekutif['pertumbuhan_kunjungan']) }}%
                    </div>
                @endif
                <a href="{{ route('divisi.dashboard', 'layanan') }}" class="kpi-link">
                    Lebih lengkap <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card">
                <div class="kpi-title">BOR</div>
                <div class="kpi-value">{{ $eksekutif['bor'] }}%</div>
                <div class="kpi-sub neutral">Target 70&ndash;85%</div>
                <a href="{{ route('divisi.dashboard', 'layanan') }}" class="kpi-link">
                    Lebih lengkap <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card">
                <div class="kpi-title">Total Pegawai Aktif</div>
                <div class="kpi-value">{{ number_format($eksekutif['total_pegawai'], 0, ',', '.') }}</div>
                <div class="kpi-sub neutral">Data kepegawaian terbaru</div>
                <a href="{{ route('divisi.dashboard', 'sdm') }}" class="kpi-link">
                    Lebih lengkap <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card">
                <div class="kpi-title">Pendapatan</div>
                <div class="kpi-value">Rp{{ number_format($eksekutif['total_pendapatan'] / 1000000, 2, ',', '.') }} Jt</div>
                @if (is_null($eksekutif['pertumbuhan_pendapatan']))
                    <div class="kpi-sub neutral">Periode sebelumnya kosong</div>
                @else
                    <div class="kpi-sub {{ $eksekutif['pertumbuhan_pendapatan'] >= 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $eksekutif['pertumbuhan_pendapatan'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                        {{ abs($eksekutif['pertumbuhan_pendapatan']) }}%
                    </div>
                @endif
                <a href="{{ route('divisi.dashboard', 'keuangan') }}" class="kpi-link">
                    Lebih lengkap <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 2 Grafik Tren --}}
    <div class="row mb-6">
        <div class="col-lg-6 mb-4">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <div class="chart-title">Tren Kunjungan Pasien</div>
                    <canvas id="chartKunjungan" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <div class="chart-title">Tren Pendapatan</div>
                    <canvas id="chartPendapatan" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const bulanKunjungan = {!! json_encode($eksekutif['kunjungan_per_bulan']->pluck('bulan')) !!};
    const totalKunjungan = {!! json_encode($eksekutif['kunjungan_per_bulan']->pluck('total')) !!};

    new Chart(document.getElementById('chartKunjungan'), {
        type: 'bar',
        data: {
            labels: bulanKunjungan,
            datasets: [{
                data: totalKunjungan,
                backgroundColor: '#5B8DEF',
                borderRadius: 6,
                maxBarThickness: 42,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    const bulanPendapatan = {!! json_encode($eksekutif['pendapatan_per_bulan']->pluck('bulan')) !!};
    const totalPendapatan = {!! json_encode($eksekutif['pendapatan_per_bulan']->pluck('pendapatan')) !!};

    new Chart(document.getElementById('chartPendapatan'), {
        type: 'line',
        data: {
            labels: bulanPendapatan,
            datasets: [{
                data: totalPendapatan,
                borderColor: '#5B8DEF',
                backgroundColor: 'rgba(91,141,239,.1)',
                tension: .3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#5B8DEF',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Rp ' + (ctx.parsed.y / 1000000).toLocaleString('id-ID', {maximumFractionDigits: 1}) + ' Jt'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => (v/1000000).toLocaleString('id-ID') + 'jt' }
                }
            }
        }
    });
</script>
@endpush