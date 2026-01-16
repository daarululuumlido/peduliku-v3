<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class UnitOrganisasi extends Model
{
    use HasUuids;

    protected $table = 'unit_organisasi';

    protected $fillable = [
        'struktur_id',
        'parent_id',
        'nama_unit',
        'kode_unit',
        'level_hierarki',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'level_hierarki' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function struktur(): BelongsTo
    {
        return $this->belongsTo(StrukturOrganisasi::class, 'struktur_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(UnitOrganisasi::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(UnitOrganisasi::class, 'parent_id');
    }

    public function masterJabatan(): HasMany
    {
        return $this->hasMany(MasterJabatan::class, 'unit_organisasi_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
}
