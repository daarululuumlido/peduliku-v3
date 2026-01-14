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
        'kepala_keluarga_id',
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
     * Get the head of family (kepala keluarga).
     */
    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(Orang::class, 'kepala_keluarga_id');
    }

    /**
     * Get all family members.
     */
    public function anggota(): HasMany
    {
        return $this->hasMany(Orang::class, 'kartu_keluarga_id');
    }
}
