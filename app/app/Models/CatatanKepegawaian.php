<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanKepegawaian extends Model
{
    use HasUuids;

    protected $table = 'catatan_kepegawaian';

    protected $fillable = [
        'peran_pegawai_id',
        'kategori',
        'judul',
        'deskripsi',
        'tgl_kejadian',
        'poin',
    ];

    protected function casts(): array
    {
        return [
            'tgl_kejadian' => 'date',
            'poin' => 'integer',
        ];
    }

    public function peranPegawai(): BelongsTo
    {
        return $this->belongsTo(PeranPegawai::class, 'peran_pegawai_id');
    }

    public function scopePrestasi($query)
    {
        return $query->where('kategori', 'PRESTASI');
    }

    public function scopePelanggaran($query)
    {
        return $query->where('kategori', 'PELANGGARAN');
    }

    public function scopeKesehatan($query)
    {
        return $query->where('kategori', 'KESEHATAN');
    }
}
