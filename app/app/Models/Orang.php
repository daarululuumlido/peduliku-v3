<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orang extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'orang';

    protected $fillable = [
        'nik',
        'nama',
        'gender',
        'tanggal_lahir',
        'tempat_lahir',
        'nama_ibu_kandung',
        'no_whatsapp',
        'alamat_ktp_id',
        'kartu_keluarga_id',
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
     * Get the family card for this person.
     */
    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
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
