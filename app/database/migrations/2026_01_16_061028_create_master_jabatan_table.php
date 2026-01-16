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
        Schema::create('master_jabatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unit_organisasi_id');
            $table->string('nama_jabatan');
            $table->boolean('is_pimpinan')->default(false);
            $table->integer('kuota_sdm')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('unit_organisasi_id')->references('id')->on('unit_organisasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jabatan');
    }
};
