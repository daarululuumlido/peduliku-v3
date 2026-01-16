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
        Schema::create('histori_jabatan_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('peran_pegawai_id');
            $table->uuid('master_jabatan_id');
            $table->string('spesialisasi')->nullable();
            $table->string('no_sk')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->text('keterangan_mutasi')->nullable();
            $table->boolean('is_jabatan_fungsional')->default(false);
            $table->timestamps();

            $table->foreign('peran_pegawai_id')->references('id')->on('peran_pegawai')->onDelete('cascade');
            $table->foreign('master_jabatan_id')->references('id')->on('master_jabatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_jabatan_pegawai');
    }
};
