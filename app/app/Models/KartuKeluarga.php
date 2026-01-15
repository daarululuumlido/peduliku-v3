<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KartuKeluarga extends Model
{
    use HasUuids;

    protected $table = 'kartu_keluarga';

    protected $fillable = [
        'no_kk',
        'alamat_id',
    ];

    /**
     * Get the address for this family card.
     */
    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    /**
     * Get all family members with their relationships.
     */
    public function anggota(): HasMany
    {
        return $this->hasMany(KartuKeluargaAnggota::class, 'kartu_keluarga_id')
            ->with('orang');
    }

    /**
     * Get head of family from relationship table.
     */
    public function kepalaKeluarga()
    {
        return $this->anggota()
            ->where('status_hubungan', 'kepala_keluarga')
            ->first();
    }
}
