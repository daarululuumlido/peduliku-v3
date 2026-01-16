<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKeluargaPegawai extends Model
{
    use HasUuids;

    protected $table = 'riwayat_keluarga_pegawai';

    protected $fillable = [
        'peran_pegawai_id',
        'status',
        'nama_pasangan',
        'jumlah_anak',
        'tgl_perubahan_status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_anak' => 'integer',
            'tgl_perubahan_status' => 'date',
        ];
    }

    public function peranPegawai(): BelongsTo
    {
        return $this->belongsTo(PeranPegawai::class, 'peran_pegawai_id');
    }
}
