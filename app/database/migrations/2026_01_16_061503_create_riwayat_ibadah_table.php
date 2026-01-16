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
        Schema::create('riwayat_ibadah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('peran_pegawai_id');
            $table->enum('jenis', ['UMROH', 'HAJI']);
            $table->date('tanggal_rencana')->nullable();
            $table->date('tanggal_berangkat')->nullable();
            $table->enum('status', ['RENCANA', 'SUDAH'])->default('RENCANA');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('peran_pegawai_id')->references('id')->on('peran_pegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_ibadah');
    }
};
