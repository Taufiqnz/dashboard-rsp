@extends('layouts.dashboard')

@section('title', 'Komposisi Pegawai')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px; padding: 30px 34px; color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .section-kicker { color: #7e8299; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .chart-wrap { min-height: 270px; display: flex; align-items: center; justify-content: center; }
    .chart-wrap canvas { max-height: 250px; }
    .stat-panel { height: 100%; padding: 24px; background: #faf8ff; border-left: 4px solid #8950fc; border-radius: 12px; }
    .stat-value { color: #6236db; font-size: 32px; line-height: 1; font-weight: 800; }
    .stat-label { color: #7e8299; font-size: 12px; font-weight: 600; }
    .group-row { padding: 14px 0; border-bottom: 1px solid #eef1f4; }
    .group-row:last-child { border-bottom: 0; }
    .group-dot { width: 10px; height: 10px; border-radius: 50%; flex: 0 0 10px; }
    .progress-track { height: 7px; background: #edf1f3; border-radius: 10px; overflow: hidden; }
    .progress-value { height: 100%; background: #8950fc; border-radius: 10px; }
    .table-modern thead th { border: none; color: #7e8299; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; }
    .table-modern td { border-color: #eef1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #faf8ff; }
    .rank-number { color: #8950fc; font-size: 18px; font-weight: 800; width: 42px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')

    @php
        $totalPegawai = $komposisi->sum('total');
        $kelompokTerbesar = $komposisi->first();
        $warnaGrafik = ['#8950FC', '#1BC5BD', '#FFA800', '#6993FF', '#F64E60'];
    @endphp

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-user-md mr-2"></i>Komposisi Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Gambaran sebaran pegawai aktif berdasarkan kelompok besar</span>
        </div>
        <div class="text-right mt-3 mt-md-0">
            <div class="text-white-50 font-size-sm">Total pegawai aktif</div>
            <div class="font-weight-bolder" style="font-size:32px; line-height:1.1;">{{ $totalPegawai }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="section-kicker">Proporsi SDM</div>
                            <h3 class="font-weight-bolder mb-0">Komposisi Kelompok</h3>
                        </div>
                        <i class="fas fa-chart-pie" style="color:#8950fc; font-size:22px;"></i>
                    </div>
                    <div class="chart-wrap"><canvas id="chartKomposisi"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <div class="section-kicker mb-1">Sorotan data</div>
                    <h3 class="font-weight-bolder mb-4">Kelompok terbesar</h3>
                    <div class="row mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="stat-panel">
                                <div class="stat-value">{{ $kelompokTerbesar['total'] ?? 0 }}</div>
                                <div class="stat-label mt-2">pegawai</div>
                                <div class="font-weight-bolder text-dark mt-3">{{ $kelompokTerbesar['label'] ?? 'Belum ada data' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stat-panel" style="border-left-color:#6236db;">
                                <div class="stat-value">{{ $kelompokTerbesar['persentase'] ?? 0 }}%</div>
                                <div class="stat-label mt-2">dari total pegawai</div>
                                <div class="font-weight-bolder text-dark mt-3">Kontribusi terbesar</div>
                            </div>
                        </div>
                    </div>
                    @foreach ($komposisi as $index => $item)
                    <div class="group-row">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="group-dot mr-2" style="background:{{ $warnaGrafik[$index % count($warnaGrafik)] }};"></span>
                                <span class="font-weight-bold text-dark font-size-sm">{{ $item['label'] }}</span>
                            </div>
                            <span class="font-weight-bolder text-dark font-size-sm">{{ $item['total'] }} <span class="text-muted font-weight-normal">({{ $item['persentase'] }}%)</span></span>
                        </div>
                        <div class="progress-track"><div class="progress-value" style="width:{{ $item['persentase'] }}%; background:{{ $warnaGrafik[$index % count($warnaGrafik)] }};"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="section-kicker">Data terperinci</div>
                    <h3 class="font-weight-bolder mb-0">Ringkasan Komposisi</h3>
                </div>
                <span class="badge badge-light font-weight-bold">{{ $komposisi->count() }} kelompok</span>
            </div>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Kelompok Besar</th>
                            <th class="text-right">Jumlah Pegawai</th>
                            <th class="text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($komposisi as $item)
                        <tr>
                            <td class="font-weight-bold text-dark"><span class="rank-number d-inline-block">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $item['label'] }}</td>
                            <td class="text-right font-weight-bold">{{ $item['total'] }}</td>
                            <td class="text-right font-weight-bold text-primary">{{ $item['persentase'] }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-6">Belum ada data komposisi pegawai</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const komposisiLabels = {!! json_encode($komposisi->pluck('label')) !!};
    const komposisiTotals = {!! json_encode($komposisi->pluck('total')) !!};

    new Chart(document.getElementById('chartKomposisi'), {
        type: 'doughnut',
        data: {
            labels: komposisiLabels,
            datasets: [{
                data: komposisiTotals,
                backgroundColor: ['#8950FC', '#1BC5BD', '#FFA800', '#6993FF', '#F64E60'],
                borderWidth: 0,
            }]
        },
        options: { responsive: true, cutout: '65%', plugins: { legend: { display: false } } }
    });
</script>
@endpush