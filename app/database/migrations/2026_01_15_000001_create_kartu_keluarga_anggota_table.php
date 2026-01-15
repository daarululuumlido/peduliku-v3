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
        Schema::create('kartu_keluarga_anggota', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kartu_keluarga_id');
            $table->uuid('orang_id');
            $table->enum('status_hubungan', [
                'kepala_keluarga',
                'suami',
                'istri',
                'anak',
                'menantu',
                'cucu',
                'orang_tua',
                'mertua',
                'famili_lain',
            ]);
            $table->timestamps();

            $table->foreign('kartu_keluarga_id')
                ->references('id')
                ->on('kartu_keluarga')
                ->onDelete('cascade');

            $table->foreign('orang_id')
                ->references('id')
                ->on('orang')
                ->onDelete('cascade');

            $table->unique(['kartu_keluarga_id', 'orang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_keluarga_anggota');
    }
};
