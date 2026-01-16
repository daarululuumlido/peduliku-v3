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
        Schema::create('riwayat_keluarga_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('peran_pegawai_id');
            $table->enum('status', ['Lajang', 'Menikah', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('nama_pasangan')->nullable();
            $table->integer('jumlah_anak')->default(0);
            $table->date('tgl_perubahan_status')->nullable();
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
        Schema::dropIfExists('riwayat_keluarga_pegawai');
    }
};
