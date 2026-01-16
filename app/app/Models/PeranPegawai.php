<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeranPegawai extends Model
{
    use HasUuids;

    protected $table = 'peran_pegawai';

    protected $fillable = [
        'orang_id',
        'nip',
        'tgl_bergabung',
        'tmt',
        'status_kepegawaian',
        'status_mukim',
        'alamat_domisili_id',
        'is_pengajar',
        'nfc_id',
        'finger_id',
        'email_internal',
        'no_rekening',
        'nuptk',
        'foto_url',
        'tgl_resign',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tgl_bergabung' => 'date',
            'tmt' => 'date',
            'tgl_resign' => 'date',
            'is_pengajar' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class, 'orang_id');
    }

    public function alamatDomisili(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_domisili_id');
    }

    public function historiJabatan(): HasMany
    {
        return $this->hasMany(HistoriJabatanPegawai::class, 'peran_pegawai_id');
    }

    public function currentJabatan(): HasMany
    {
        return $this->historiJabatan()->whereNull('tgl_selesai')->latest();
    }

    public function riwayatKeluarga(): HasMany
    {
        return $this->hasMany(RiwayatKeluargaPegawai::class, 'peran_pegawai_id');
    }

    public function riwayatIbadah(): HasMany
    {
        return $this->hasMany(RiwayatIbadah::class, 'peran_pegawai_id');
    }

    public function catatanKepegawaian(): HasMany
    {
        return $this->hasMany(CatatanKepegawaian::class, 'peran_pegawai_id');
    }

    public function checklistDokumen(): HasMany
    {
        return $this->hasMany(ChecklistDokumenPegawai::class, 'peran_pegawai_id');
    }

    public function riwayatPendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class, 'peran_pegawai_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePengajar($query)
    {
        return $query->where('is_pengajar', true);
    }
}
