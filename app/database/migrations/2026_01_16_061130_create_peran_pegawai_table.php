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
        Schema::create('peran_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('orang_id');
            $table->string('nip')->unique();
            $table->date('tgl_bergabung');
            $table->date('tmt')->nullable();
            $table->enum('status_kepegawaian', ['Guru Tetap', 'Guru Kontrak', 'Karyawan Tetap', 'Karyawan Kontrak', 'Honorer'])->default('Honorer');
            $table->enum('status_mukim', ['Aktif', 'Tidak Aktif'])->nullable();
            $table->uuid('alamat_domisili_id')->nullable();
            $table->boolean('is_pengajar')->default(false);
            $table->string('nfc_id')->nullable();
            $table->string('finger_id')->nullable();
            $table->string('email_internal')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('foto_url')->nullable();
            $table->date('tgl_resign')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('orang_id')->references('id')->on('orang')->onDelete('cascade');
            $table->foreign('alamat_domisili_id')->references('id')->on('alamat')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peran_pegawai');
    }
};
