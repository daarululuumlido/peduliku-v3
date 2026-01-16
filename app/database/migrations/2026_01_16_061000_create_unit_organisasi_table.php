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
        Schema::create('unit_organisasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('struktur_id');
            $table->uuid('parent_id')->nullable();
            $table->string('nama_unit');
            $table->string('kode_unit')->nullable();
            $table->integer('level_hierarki')->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('struktur_id')->references('id')->on('struktur_organisasi')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('unit_organisasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_organisasi');
    }
};
