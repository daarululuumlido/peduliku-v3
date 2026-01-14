<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\Village;

class Alamat extends Model
{
    use HasUuids;

    protected $table = 'alamat';

    protected $fillable = [
        'desa_id',
        'alamat_lengkap',
    ];

    protected $appends = ['full_address'];

    /**
     * Get the village (desa) for this address.
     */
    public function desa(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'desa_id', 'code');
    }

    /**
     * Get the people linked to this address.
     */
    public function orang()
    {
        return $this->hasMany(Orang::class, 'alamat_ktp_id');
    }

    /**
     * Get full address string including village hierarchy.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->alamat_lengkap) {
            $parts[] = $this->alamat_lengkap;
        }

        if ($this->desa) {
            $parts[] = $this->desa->name;
            $parts[] = 'Kec. '.$this->desa->district->name;
            $parts[] = $this->desa->district->city->name;
            $parts[] = $this->desa->district->city->province->name;
        }

        return implode(', ', $parts);
    }
}
