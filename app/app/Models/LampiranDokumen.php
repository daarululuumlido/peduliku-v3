<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LampiranDokumen extends Model
{
    use HasUuids;

    protected $table = 'lampiran_dokumen';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'nama_file',
        'path',
        'mime_type',
        'size',
    ];

    /**
     * Get the parent documentable model (Orang, etc).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}
