<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nik',
        'nama',
        'profesi_id',
        'unit_kerja_id',
        'jenis_kelamin',
        'kelamin',
        'kelompok_besar',
        'direktorat',
        'tanggal_lahir',
        'tempat_lahir',
        'tanggal_masuk',
        'status_kepegawaian',
        'status_pegawai',
        'pendidikan',
        'jabatan',
        'golongan',
        'jenis_kerja',
        'no_hp',
        'alamat',
        'user_id',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function profesi(): BelongsTo
    {
        return $this->belongsTo(Profesi::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalShift(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JadwalShift::class);
    }
}
