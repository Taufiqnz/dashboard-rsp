@extends('pdf.layout')

@section('title', 'Data Pegawai')

@section('content')
<table>
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
            <td>{{ $p->nama }}</td>
            <td>
                @if ($p->status_pegawai)
                    {{ $p->status_pegawai }}
                @else
                    <span style="color:#999; font-style:italic;">Belum diisi</span>
                @endif
            </td>
            <td>{{ $p->nip }}</td>
            <td>{{ $p->kelamin ?? '-' }}</td>
            <td>
                @if ($p->kelompok_besar)
                    {{ $p->kelompok_besar }}
                @else
                    <span style="color:#999; font-style:italic;">Belum diisi</span>
                @endif
            </td>
            <td>
                @if ($p->golongan)
                    {{ $p->golongan }}
                @else
                    <span style="color:#999; font-style:italic;">Belum diisi</span>
                @endif
            </td>
            <td>{{ $p->direktorat ?? '-' }}</td>
            <td>{{ $p->unitKerja?->nama_unit ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;">Tidak ada pegawai yang cocok</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">
    Total: {{ $pegawai->count() }} pegawai aktif
    @if ($cari)
        &middot; Kata kunci pencarian: "{{ $cari }}"
    @endif
</p>
@endsection