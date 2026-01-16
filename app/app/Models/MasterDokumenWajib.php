<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDokumenWajib extends Model
{
    use HasUuids;

    protected $table = 'master_dokumen_wajib';

    protected $fillable = [
        'nama_dokumen',
        'wajib_untuk',
        'keterangan',
        'is_wajib',
    ];

    protected function casts(): array
    {
        return [
            'is_wajib' => 'boolean',
        ];
    }

    public function checklistDokumen(): HasMany
    {
        return $this->hasMany(ChecklistDokumenPegawai::class, 'master_dokumen_wajib_id');
    }

    public function scopePegawai($query)
    {
        return $query->where('wajib_untuk', 'PEGAWAI');
    }

    public function scopeSantri($query)
    {
        return $query->where('wajib_untuk', 'SANTRI');
    }

    public function scopeWajib($query)
    {
        return $query->where('is_wajib', true);
    }
}
