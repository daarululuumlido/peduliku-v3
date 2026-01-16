<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistDokumenPegawai extends Model
{
    use HasUuids;

    protected $table = 'checklist_dokumen_pegawai';

    protected $fillable = [
        'peran_pegawai_id',
        'master_dokumen_wajib_id',
        'lampiran_dokumen_id',
        'terpenuhi',
        'tgl_verifikasi',
        'diverifikasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'terpenuhi' => 'boolean',
            'tgl_verifikasi' => 'date',
        ];
    }

    public function peranPegawai(): BelongsTo
    {
        return $this->belongsTo(PeranPegawai::class, 'peran_pegawai_id');
    }

    public function masterDokumenWajib(): BelongsTo
    {
        return $this->belongsTo(MasterDokumenWajib::class, 'master_dokumen_wajib_id');
    }

    public function lampiranDokumen(): BelongsTo
    {
        return $this->belongsTo(LampiranDokumen::class, 'lampiran_dokumen_id');
    }

    public function scopeTerpenuhi($query)
    {
        return $query->where('terpenuhi', true);
    }

    public function scopeBelumTerpenuhi($query)
    {
        return $query->where('terpenuhi', false);
    }

    public function scopeTerverifikasi($query)
    {
        return $query->whereNotNull('tgl_verifikasi');
    }
}
