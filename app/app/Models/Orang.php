<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orang extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'orang';

    protected $fillable = [
        'nik',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'gender',
        'tanggal_lahir',
        'tempat_lahir',
        'nama_ibu_kandung',
        'no_whatsapp',
        'alamat_ktp_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the KTP address for this person.
     */
    public function alamatKtp(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_ktp_id');
    }

    /**
     * Get family membership records for this person.
     */
    public function kartuKeluargaAnggota()
    {
        return $this->hasMany(KartuKeluargaAnggota::class, 'orang_id')
            ->with('kartuKeluarga');
    }

    /**
     * Get current family membership.
     */
    public function keanggotaanKeluargaSaatIni()
    {
        return $this->kartuKeluargaAnggota()->first();
    }

    /**
     * Get current relationship status.
     */
    public function getStatusHubunganSaatIniAttribute()
    {
        $keanggotaan = $this->keanggotaanKeluargaSaatIni();

        return $keanggotaan ? $keanggotaan->status_hubungan : null;
    }

    /**
     * Get all document attachments for this person.
     */
    public function dokumen(): MorphMany
    {
        return $this->morphMany(LampiranDokumen::class, 'documentable');
    }

    /**
     * Get full name.
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama;
    }

    /**
     * Get gender label.
     */
    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}
