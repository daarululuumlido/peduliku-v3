<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendidikan extends Model
{
    use HasUuids;

    protected $table = 'riwayat_pendidikan';

    protected $fillable = [
        'orang_id',
        'jenjang',
        'institusi',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'nilai_akhir',
        'no_ijazah',
    ];

    protected function casts(): array
    {
        return [
            'tahun_masuk' => 'integer',
            'tahun_lulus' => 'integer',
        ];
    }

    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class, 'orang_id');
    }
}
