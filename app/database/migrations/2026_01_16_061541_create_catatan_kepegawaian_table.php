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
        Schema::create('catatan_kepegawaian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('peran_pegawai_id');
            $table->enum('kategori', ['PRESTASI', 'PELANGGARAN', 'KESEHATAN', 'LAINNYA']);
            $table->string('judul');
            $table->text('deskripsi');
            $table->date('tgl_kejadian');
            $table->integer('poin')->default(0);
            $table->timestamps();

            $table->foreign('peran_pegawai_id')->references('id')->on('peran_pegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_kepegawaian');
    }
};
