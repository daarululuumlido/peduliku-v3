<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KartuKeluargaAnggota extends Model
{
    use HasUuids;

    protected $table = 'kartu_keluarga_anggota';

    protected $fillable = [
        'kartu_keluarga_id',
        'orang_id',
        'status_hubungan',
    ];

    /**
     * Get the family card for this membership.
     */
    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    /**
     * Get the person for this membership.
     */
    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class, 'orang_id');
    }

    /**
     * Get status hubungan label in Indonesian.
     */
    public function getStatusHubunganLabelAttribute(): string
    {
        return match ($this->status_hubungan) {
            'kepala_keluarga' => 'Kepala Keluarga',
            'suami' => 'Suami',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orang_tua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            default => $this->status_hubungan,
        };
    }

    /**
     * Get all possible status hubungan options.
     */
    public static function getStatusHubunganOptions(): array
    {
        return [
            'kepala_keluarga' => 'Kepala Keluarga',
            'suami' => 'Suami',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orang_tua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
        ];
    }
}
