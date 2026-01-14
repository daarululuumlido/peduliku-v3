<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lampiran_dokumen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('documentable_type');
            $table->uuid('documentable_id');
            $table->string('nama_file');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id'], 'lampiran_dokumen_morphs_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran_dokumen');
    }
};
