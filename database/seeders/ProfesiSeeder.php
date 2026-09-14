<?php

namespace Database\Seeders;

use App\Models\Profesi;
use Illuminate\Database\Seeder;

class ProfesiSeeder extends Seeder
{
    public function run(): void
    {
        $profesi = [
            // kategori: medis
            ['nama_profesi' => 'Dokter Umum', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Spesialis Anak', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Spesialis Kandungan', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Spesialis Bedah', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Spesialis Penyakit Dalam', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Spesialis Jantung', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Gigi', 'kategori' => 'medis'],
            ['nama_profesi' => 'Dokter Spesialis', 'kategori' => 'medis'], // generik, dari data pegawai asli
            ['nama_profesi' => 'Dokter Sub Spesialis', 'kategori' => 'medis'], // generik, dari data pegawai asli

            // kategori: keperawatan
            ['nama_profesi' => 'Perawat', 'kategori' => 'keperawatan'],
            ['nama_profesi' => 'Bidan', 'kategori' => 'keperawatan'],

            // kategori: nakes_lain
            ['nama_profesi' => 'Apoteker', 'kategori' => 'nakes_lain'],
            ['nama_profesi' => 'Analis Laboratorium', 'kategori' => 'nakes_lain'],
            ['nama_profesi' => 'Radiografer', 'kategori' => 'nakes_lain'],
            ['nama_profesi' => 'Nakes Lain', 'kategori' => 'nakes_lain'], // generik, dari data pegawai asli

            // kategori: nonkesehatan
            ['nama_profesi' => 'Staf Administrasi', 'kategori' => 'nonkesehatan'],
            ['nama_profesi' => 'Administrasi', 'kategori' => 'nonkesehatan'], // generik, dari data pegawai asli
            ['nama_profesi' => 'Petugas Keamanan', 'kategori' => 'nonkesehatan'],
            ['nama_profesi' => 'Cleaning Service', 'kategori' => 'nonkesehatan'],
        ];

        foreach ($profesi as $p) {
            Profesi::updateOrCreate(['nama_profesi' => $p['nama_profesi']], $p);
        }
    }
}
