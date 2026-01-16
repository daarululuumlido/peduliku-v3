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
        Schema::table('orang', function (Blueprint $table) {
            $table->json('custom_attribute')->nullable()->after('alamat_ktp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orang', function (Blueprint $table) {
            $table->dropColumn('custom_attribute');
        });
    }
};
