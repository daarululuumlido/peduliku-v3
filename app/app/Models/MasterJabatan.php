<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterJabatan extends Model
{
    use HasUuids;

    protected $table = 'master_jabatan';

    protected $fillable = [
        'unit_organisasi_id',
        'nama_jabatan',
        'is_pimpinan',
        'kuota_sdm',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'is_pimpinan' => 'boolean',
            'kuota_sdm' => 'integer',
        ];
    }

    public function unitOrganisasi(): BelongsTo
    {
        return $this->belongsTo(UnitOrganisasi::class, 'unit_organisasi_id');
    }

    public function historiJabatan(): HasMany
    {
        return $this->hasMany(HistoriJabatanPegawai::class, 'master_jabatan_id');
    }
}
