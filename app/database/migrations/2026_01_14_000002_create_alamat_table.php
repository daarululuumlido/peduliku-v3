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
        Schema::create('alamat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('desa_id', 10)->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->timestamps();

            $table->foreign('desa_id')
                ->references('code')
                ->on('indonesia_villages')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};
