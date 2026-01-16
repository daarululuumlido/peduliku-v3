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
        Schema::create('master_dokumen_wajib', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_dokumen');
            $table->enum('wajib_untuk', ['PEGAWAI', 'SANTRI', 'SEMUA']);
            $table->text('keterangan')->nullable();
            $table->boolean('is_wajib')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_dokumen_wajib');
    }
};
