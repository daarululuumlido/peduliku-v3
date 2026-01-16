<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StrukturOrganisasi extends Model
{
    use HasUuids;

    protected $table = 'struktur_organisasi';

    protected $fillable = [
        'nama_periode',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function unitOrganisasi(): HasMany
    {
        return $this->hasMany(UnitOrganisasi::class, 'struktur_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
