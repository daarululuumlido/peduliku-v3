<?php

namespace App\Services;

use App\Models\LampiranDokumen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    protected string $disk;

    public function __construct()
    {
        // Use 'dokumen' for local, 'dokumen_s3' for S3
        $this->disk = env('DOCUMENT_DISK', 'dokumen');
    }

    /**
     * Upload a document and attach to a model.
     */
    public function upload(UploadedFile $file, Model $model, ?string $customName = null): LampiranDokumen
    {
        $fileName = $customName ?? $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique path: {model_type}/{model_id}/{uuid}.{ext}
        $modelType = class_basename($model);
        $path = sprintf(
            '%s/%s/%s.%s',
            Str::snake($modelType),
            $model->id,
            Str::uuid(),
            $extension
        );

        // Store file
        Storage::disk($this->disk)->put($path, file_get_contents($file));

        // Create database record
        return LampiranDokumen::create([
            'documentable_type' => get_class($model),
            'documentable_id' => $model->id,
            'nama_file' => $fileName,
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);
    }

    /**
     * Get file URL (for private files, generates temporary URL).
     */
    public function getUrl(LampiranDokumen $dokumen, int $expiresMinutes = 60): ?string
    {
        if (! Storage::disk($this->disk)->exists($dokumen->path)) {
            return null;
        }

        // For S3, generate temporary URL
        if ($this->disk === 'dokumen_s3') {
            return Storage::disk($this->disk)->temporaryUrl(
                $dokumen->path,
                now()->addMinutes($expiresMinutes)
            );
        }

        // For local, return download route
        return route('dokumen.download', $dokumen->id);
    }

    /**
     * Download file content.
     */
    public function download(LampiranDokumen $dokumen): ?string
    {
        if (! Storage::disk($this->disk)->exists($dokumen->path)) {
            return null;
        }

        return Storage::disk($this->disk)->get($dokumen->path);
    }

    /**
     * Delete a document.
     */
    public function delete(LampiranDokumen $dokumen): bool
    {
        // Delete file from storage
        if (Storage::disk($this->disk)->exists($dokumen->path)) {
            Storage::disk($this->disk)->delete($dokumen->path);
        }

        // Delete database record
        return $dokumen->delete();
    }

    /**
     * Get all documents for a model.
     */
    public function getDocuments(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return LampiranDokumen::where('documentable_type', get_class($model))
            ->where('documentable_id', $model->id)
            ->get();
    }
}
