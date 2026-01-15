<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    protected $table = 'audits';

    protected $fillable = [
        'user_id',
        'user_type',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'restored' => 'Dikembalikan',
            default => ucfirst($this->event),
        };
    }

    public function getAuditableLabelAttribute(): string
    {
        return match ($this->auditable_type) {
            'App\Models\Orang' => 'Orang',
            'App\Models\KartuKeluarga' => 'Kartu Keluarga',
            'App\Models\KartuKeluargaAnggota' => 'Anggota KK',
            'App\Models\Alamat' => 'Alamat',
            'App\Models\User' => 'User',
            default => $this->auditable_type,
        };
    }
}
