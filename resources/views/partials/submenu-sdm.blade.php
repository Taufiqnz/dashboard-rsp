@php
    $menuSdm = [
        ['label' => 'Ringkasan', 'route' => 'divisi.dashboard'],
        ['label' => 'Data Pegawai', 'route' => 'divisi.sdm.data-pegawai'],
        ['label' => 'Komposisi Pegawai', 'route' => 'divisi.sdm.komposisi'],
        // ['label' => 'Kehadiran', 'route' => null], // nonaktif: belum ada data absensi asli
        // ['label' => 'Cuti & Izin', 'route' => null], // nonaktif: belum ada data cuti/izin asli
        // ['label' => 'Jadwal Kerja', 'route' => null], // nonaktif: belum ada data jadwal shift asli
        ['label' => 'Distribusi Pegawai', 'route' => 'divisi.sdm.distribusi'],
        // ['label' => 'Pelatihan', 'route' => null], // nonaktif: belum ada data riwayat pelatihan asli
        // ['label' => 'Produktivitas', 'route' => null], // nonaktif: unit kerja pegawai asli belum ke-link ke data kunjungan Layanan
    ];
@endphp

<style>
    .submenu-layanan {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        overflow-x: auto;
        margin-bottom: 24px;
    }
    .submenu-layanan .submenu-track {
        display: flex;
        min-width: max-content;
        padding: 8px;
        gap: 4px;
    }
    .submenu-item {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
        color: #7e8299;
    }
    .submenu-item.aktif {
        background: #F1E9FF;
        color: #8950FC;
    }
    .submenu-item.nonaktif {
        color: #c4c7d5;
        cursor: not-allowed;
    }
    .submenu-item:not(.nonaktif):not(.aktif):hover {
        background: #f4f6f9;
        color: #464E5F;
    }
</style>

<div class="submenu-layanan">
    <div class="submenu-track">
        @foreach ($menuSdm as $menu)
            @if ($menu['route'])
                <a href="{{ route($menu['route'], $division->slug) }}"
                   class="submenu-item {{ request()->routeIs($menu['route']) ? 'aktif' : '' }}">
                    {{ $menu['label'] }}
                </a>
            @else
                <span class="submenu-item nonaktif" title="Segera hadir">
                    {{ $menu['label'] }}
                </span>
            @endif
        @endforeach
    </div>
</div>