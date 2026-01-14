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
        Schema::create('orang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('gelar_depan', 50)->nullable();
            $table->string('gelar_belakang', 50)->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('no_whatsapp', 20)->nullable();
            $table->uuid('alamat_ktp_id')->nullable();
            $table->uuid('kartu_keluarga_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('alamat_ktp_id')
                ->references('id')
                ->on('alamat')
                ->onDelete('set null');

            $table->foreign('kartu_keluarga_id')
                ->references('id')
                ->on('kartu_keluarga')
                ->onDelete('set null');
        });

        // Add kepala_keluarga_id foreign key after orang table is created
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            $table->foreign('kepala_keluarga_id')
                ->references('id')
                ->on('orang')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            $table->dropForeign(['kepala_keluarga_id']);
        });

        Schema::dropIfExists('orang');
    }
};
