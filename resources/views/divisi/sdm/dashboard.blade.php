@extends('layouts.dashboard')

@section('title', 'Dashboard SDM')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }

    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }

    .filter-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        border: none;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--accent, #8950FC);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,.1);
    }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        margin-bottom: 14px;
        box-shadow: 0 4px 10px rgba(0,0,0,.06);
    }
    .stat-value { font-size: 28px; font-weight: 800; line-height: 1.1; }
    .stat-label {
        font-size: 12px; font-weight: 700; color: #7e8299; margin-top: 6px;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .stat-sub { font-size: 12px; font-weight: 600; color: #a1a5b7; margin-top: 4px; }

    /* Kartu hero: Jumlah Pegawai */
    .hero-card {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px;
        padding: 26px 30px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.22);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
        gap: 20px;
    }
    .hero-icon {
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; flex-shrink: 0;
    }
    .hero-value { font-size: 38px; font-weight: 800; line-height: 1; }
    .hero-label { font-size: 13px; font-weight: 600; color: rgba(255,255,255,.85); margin-top: 4px; }
    .hero-chip {
        background: rgba(255,255,255,.15);
        border-radius: 12px;
        padding: 10px 16px;
        text-align: center;
        min-width: 90px;
    }
    .hero-chip .chip-value { font-size: 18px; font-weight: 800; }
    .hero-chip .chip-label { font-size: 11px; font-weight: 600; color: rgba(255,255,255,.8); margin-top: 2px; }

    .section-title {
        font-size: 14px; font-weight: 700; color: #a1a5b7;
        text-transform: uppercase; letter-spacing: .6px;
        margin: 28px 0 14px;
        display: flex; align-items: center; gap: 10px;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #eaeaf0;
    }

    .modern-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
    }
    .modern-card .card-title {
        font-weight: 700; font-size: 17px; color: #181c32;
    }

    .table-modern thead th {
        border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;
    }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }

    .badge-modern {
        border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px;
    }

    .unit-bar-bg { background: #f1f1f4; border-radius: 10px; height: 6px; overflow: hidden; margin-top: 6px; }
    .unit-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg,#8950FC,#6236DB); }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')
    {{-- Header + Filter --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-users mr-2"></i>Dashboard SDM</h1>
            <span class="text-muted-light font-weight-bold">Statistik kepegawaian & sumber daya manusia rumah sakit</span>
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
        <button type="submit" class="btn btn-primary font-weight-bold px-6"><i class="fas fa-filter mr-2"></i>Terapkan</button>
    </form>

    {{-- Ringkasan: Jumlah Pegawai (hero) + Komposisi Tenaga + Status Kepegawaian --}}
    @php
        // Ambil dari komposisi_sdm yang udah dihitung service (dokter/perawat/nakes_lain/administrasi/pendukung)
        $totalPerKelompok = $data['komposisi_sdm']->pluck('total', 'kelompok');
        $jumlahDokter = $totalPerKelompok['dokter'] ?? 0;
        $jumlahPerawat = $totalPerKelompok['perawat'] ?? 0;
        $jumlahPenunjang = $totalPerKelompok['nakes_lain'] ?? 0;
        $jumlahAdministrasi = ($totalPerKelompok['administrasi'] ?? 0) + ($totalPerKelompok['pendukung'] ?? 0);
        $totalPegawai = max($data['total_pegawai'], 1); // hindari divide by zero

        $persen = fn($n) => round(($n / $totalPegawai) * 100, 1);
    @endphp

    {{-- Hero: Jumlah Pegawai --}}
    <div class="hero-card mb-4">
        <div class="d-flex align-items-center" style="gap: 20px;">
            <div class="hero-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="hero-value">{{ number_format($data['total_pegawai']) }}</div>
                <div class="hero-label">Total Pegawai Aktif</div>
            </div>
        </div>
        <div class="d-flex" style="gap: 12px; flex-wrap: wrap;">
            <div class="hero-chip">
                <div class="chip-value">{{ $jumlahDokter }}</div>
                <div class="chip-label">Dokter</div>
            </div>
            <div class="hero-chip">
                <div class="chip-value">{{ $jumlahPerawat }}</div>
                <div class="chip-label">Perawat</div>
            </div>
            <div class="hero-chip">
                <div class="chip-value">{{ $jumlahPenunjang }}</div>
                <div class="chip-label">Penunjang</div>
            </div>
            <div class="hero-chip">
                <div class="chip-value">{{ $jumlahAdministrasi }}</div>
                <div class="chip-label">Administrasi</div>
            </div>
        </div>
    </div>

    {{-- Komposisi Tenaga Kerja --}}
    <div class="section-title"><i class="fas fa-layer-group"></i> Komposisi Tenaga Kerja</div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#6993FF;"><div class="card-body">
                <div class="stat-icon" style="background:#EEF3FF; color:#6993FF;"><i class="fas fa-user-doctor fa-user-md"></i></div>
                <div class="stat-value text-dark">{{ number_format($jumlahDokter) }}</div>
                <div class="stat-label">Dokter</div>
                <div class="stat-sub">{{ $persen($jumlahDokter) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#1BC5BD;"><div class="card-body">
                <div class="stat-icon" style="background:#E8FFF3; color:#1BC5BD;"><i class="fas fa-user-nurse"></i></div>
                <div class="stat-value text-dark">{{ number_format($jumlahPerawat) }}</div>
                <div class="stat-label">Perawat</div>
                <div class="stat-sub">{{ $persen($jumlahPerawat) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#FF8A3D;"><div class="card-body">
                <div class="stat-icon" style="background:#FFF0E6; color:#FF8A3D;"><i class="fas fa-flask"></i></div>
                <div class="stat-value text-dark">{{ number_format($jumlahPenunjang) }}</div>
                <div class="stat-label">Penunjang</div>
                <div class="stat-sub">{{ $persen($jumlahPenunjang) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#FFA800;"><div class="card-body">
                <div class="stat-icon" style="background:#FFF6E0; color:#FFA800;"><i class="fas fa-briefcase"></i></div>
                <div class="stat-value text-dark">{{ number_format($jumlahAdministrasi) }}</div>
                <div class="stat-label">Administrasi</div>
                <div class="stat-sub">{{ $persen($jumlahAdministrasi) }}% dari total pegawai</div>
            </div></div>
        </div>
    </div>

    {{-- Status Kepegawaian --}}
    <div class="section-title"><i class="fas fa-id-badge"></i> Status Kepegawaian</div>
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#8950FC;"><div class="card-body">
                <div class="stat-icon" style="background:#F1E9FF; color:#8950FC;"><i class="fas fa-landmark"></i></div>
                <div class="stat-value text-dark">{{ number_format($data['status_kepegawaian']['pns']) }}</div>
                <div class="stat-label">PNS</div>
                <div class="stat-sub">{{ $persen($data['status_kepegawaian']['pns']) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#6993FF;"><div class="card-body">
                <div class="stat-icon" style="background:#EEF3FF; color:#6993FF;"><i class="fas fa-file-signature"></i></div>
                <div class="stat-value text-dark">{{ number_format($data['status_kepegawaian']['pppk']) }}</div>
                <div class="stat-label">PPPK</div>
                <div class="stat-sub">{{ $persen($data['status_kepegawaian']['pppk']) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#F64E60;"><div class="card-body">
                <div class="stat-icon" style="background:#FFE9EA; color:#F64E60;"><i class="fas fa-file-contract"></i></div>
                <div class="stat-value text-dark">{{ number_format($data['status_kepegawaian']['blu']) }}</div>
                <div class="stat-label">BLU</div>
                <div class="stat-sub">{{ $persen($data['status_kepegawaian']['blu']) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#FF8A3D;"><div class="card-body">
                <div class="stat-icon" style="background:#FFF0E6; color:#FF8A3D;"><i class="fas fa-handshake"></i></div>
                <div class="stat-value text-dark">{{ number_format($data['status_kepegawaian']['mitra']) }}</div>
                <div class="stat-label">Mitra</div>
                <div class="stat-sub">{{ $persen($data['status_kepegawaian']['mitra']) }}% dari total pegawai</div>
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card stat-card" style="--accent:#7e8299;"><div class="card-body">
                <div class="stat-icon" style="background:#F3F6F9; color:#7e8299;"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-value text-dark">{{ number_format($data['status_kepegawaian']['magang']) }}</div>
                <div class="stat-label">Magang</div>
                <div class="stat-sub">{{ $persen($data['status_kepegawaian']['magang']) }}% dari total pegawai</div>
            </div></div>
        </div>
    </div>

    <div class="row">
        {{-- Komposisi SDM (donut) --}}
        <div class="col-lg-5 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <h3 class="card-title mb-4"><i class="fas fa-user-md text-primary mr-2"></i>Komposisi SDM</h3>
                    <canvas id="chartKomposisi" height="220"></canvas>
                    <div class="mt-4">
                        @foreach ($data['komposisi_sdm'] as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-bold text-dark font-size-sm">{{ $item['label'] }}</span>
                            <span class="font-weight-bolder text-primary font-size-sm">{{ $item['total'] }} ({{ $item['persentase'] }}%)</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribusi per Unit Kerja --}}
        <div class="col-lg-7 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <h3 class="card-title mb-4"><i class="fas fa-building text-primary mr-2"></i>Distribusi Pegawai per Unit Kerja</h3>
                    @php $maxUnit = $data['distribusi_per_unit']->max('total') ?: 1; @endphp
                    @forelse ($data['distribusi_per_unit'] as $unit)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold text-dark font-size-sm">{{ $unit->nama_unit }}</span>
                            <span class="font-weight-bolder text-primary font-size-sm">{{ $unit->total }}</span>
                        </div>
                        <div class="unit-bar-bg">
                            <div class="unit-bar-fill" style="width: {{ ($unit->total / $maxUnit) * 100 }}%;"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Rekap Status Absensi --}}
    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="card-title mb-4"><i class="fas fa-calendar-check text-primary mr-2"></i>Rekap Kehadiran per Status (Periode Terpilih)</h3>
            @php
                $statusLabel = [
                    'hadir' => 'Hadir',
                    'terlambat' => 'Terlambat',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpha' => 'Alpha',
                ];
                $statusColor = [
                    'hadir' => ['#E8FFF3', '#1BC5BD'],
                    'terlambat' => ['#FFF6E0', '#FFA800'],
                    'izin' => ['#EEF3FF', '#6993FF'],
                    'sakit' => ['#F1E9FF', '#8950FC'],
                    'alpha' => ['#FFE9EA', '#F64E60'],
                ];
            @endphp
            <table class="table table-modern">
                <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
                <tbody>
                    @forelse ($data['rekap_status_absensi'] as $status => $total)
                    <tr>
                        <td>
                            <span class="badge badge-modern" style="background:{{ $statusColor[$status][0] ?? '#F3F6F9' }}; color:{{ $statusColor[$status][1] ?? '#464E5F' }};">
                                {{ $statusLabel[$status] ?? ucfirst($status) }}
                            </span>
                        </td>
                        <td class="font-weight-bold text-dark">{{ $total }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-muted">Belum ada data absensi pada periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Indikator Kehadiran Bulanan: Target vs Realisasi --}}
    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="card-title mb-4"><i class="fas fa-calendar-alt text-primary mr-2"></i>Indikator Kehadiran Bulanan</h3>
            <p class="text-muted font-size-sm mb-4">Perbandingan target kehadiran dengan realisasi tiap bulan ({{ $data['kehadiran_bulanan']->count() }} bulan terakhir)</p>
            <table class="table table-modern">
                <thead><tr><th>Bulan</th><th>Target Kehadiran</th><th>Realisasi</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($data['kehadiran_bulanan'] as $row)
                    <tr>
                        <td class="font-weight-bold text-dark">{{ $row['bulan'] }}</td>
                        <td>{{ $row['target'] }}%</td>
                        <td class="font-weight-bold">{{ $row['realisasi'] }}%</td>
                        <td>
                            @if ($row['status'] === 'Baik')
                                <span class="badge badge-modern" style="background:#E8FFF3; color:#1BC5BD;">Baik</span>
                            @elseif ($row['status'] === 'Sesuai target')
                                <span class="badge badge-modern" style="background:#EEF3FF; color:#6993FF;">Sesuai target</span>
                            @else
                                <span class="badge badge-modern" style="background:#FFF6E0; color:#FFA800;">Perlu perhatian</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted">Belum ada data kehadiran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const komposisiLabels = {!! json_encode($data['komposisi_sdm']->pluck('label')) !!};
    const komposisiTotals = {!! json_encode($data['komposisi_sdm']->pluck('total')) !!};

    const ctxKomposisi = document.getElementById('chartKomposisi');
    new Chart(ctxKomposisi, {
        type: 'doughnut',
        data: {
            labels: komposisiLabels,
            datasets: [{
                data: komposisiTotals,
                backgroundColor: ['#8950FC', '#1BC5BD', '#FFA800', '#6993FF', '#F64E60'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush