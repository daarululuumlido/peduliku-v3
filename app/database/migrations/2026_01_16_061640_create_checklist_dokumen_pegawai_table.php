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
        Schema::create('checklist_dokumen_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('peran_pegawai_id');
            $table->uuid('master_dokumen_wajib_id');
            $table->uuid('lampiran_dokumen_id')->nullable();
            $table->boolean('terpenuhi')->default(false);
            $table->date('tgl_verifikasi')->nullable();
            $table->string('diverifikasi_oleh')->nullable();
            $table->timestamps();

            $table->foreign('peran_pegawai_id')->references('id')->on('peran_pegawai')->onDelete('cascade');
            $table->foreign('master_dokumen_wajib_id')->references('id')->on('master_dokumen_wajib')->onDelete('cascade');
            $table->foreign('lampiran_dokumen_id')->references('id')->on('lampiran_dokumen')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_dokumen_pegawai');
    }
};
