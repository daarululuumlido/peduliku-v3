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
        Schema::create('riwayat_pendidikan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('orang_id');
            $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3', 'Pesantren', 'Lainnya']);
            $table->string('institusi');
            $table->string('jurusan')->nullable();
            $table->integer('tahun_masuk');
            $table->integer('tahun_lulus')->nullable();
            $table->string('nilai_akhir')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->timestamps();

            $table->foreign('orang_id')->references('id')->on('orang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikan');
    }
};
