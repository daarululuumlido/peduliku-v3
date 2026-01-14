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
        Schema::create('kartu_keluarga', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_kk', 16)->unique();
            $table->uuid('kepala_keluarga_id')->nullable();
            $table->uuid('alamat_id')->nullable();
            $table->timestamps();

            $table->foreign('alamat_id')
                ->references('id')
                ->on('alamat')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_keluarga');
    }
};
