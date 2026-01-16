<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatIbadah extends Model
{
    use HasUuids;

    protected $table = 'riwayat_ibadah';

    protected $fillable = [
        'peran_pegawai_id',
        'jenis',
        'tanggal_rencana',
        'tanggal_berangkat',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_rencana' => 'date',
            'tanggal_berangkat' => 'date',
        ];
    }

    public function peranPegawai(): BelongsTo
    {
        return $this->belongsTo(PeranPegawai::class, 'peran_pegawai_id');
    }

    public function scopeTerlaksana($query)
    {
        return $query->where('status', 'SUDAH');
    }

    public function scopeRencana($query)
    {
        return $query->where('status', 'RENCANA');
    }
}
