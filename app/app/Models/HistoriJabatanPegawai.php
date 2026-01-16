<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriJabatanPegawai extends Model
{
    use HasUuids;

    protected $table = 'histori_jabatan_pegawai';

    protected $fillable = [
        'peran_pegawai_id',
        'master_jabatan_id',
        'spesialisasi',
        'no_sk',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan_mutasi',
        'is_jabatan_fungsional',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'is_jabatan_fungsional' => 'boolean',
        ];
    }

    public function peranPegawai(): BelongsTo
    {
        return $this->belongsTo(PeranPegawai::class, 'peran_pegawai_id');
    }

    public function masterJabatan(): BelongsTo
    {
        return $this->belongsTo(MasterJabatan::class, 'master_jabatan_id');
    }

    public function scopeAktif($query)
    {
        return $query->whereNull('tgl_selesai')
            ->orWhere('tgl_selesai', '>=', now());
    }
}
