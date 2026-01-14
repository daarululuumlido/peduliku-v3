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
        Schema::table('alamat', function (Blueprint $table) {
            // Drop constraint lama yang salah (referencing 'id')
            // SQLite di Laravel hanya support dropForeign jika DBAL terinstall, 
            // tapi karena ini file baru kita coba drop constraint by name.
            // Jika driver SQLite, dropForeign mungkin tidak jalan mulus tanpa trik, 
            // tapi kita coba standar Laravel dulu.
            
            // Perlu dicatat: nama constraint default di Laravel biasanya 'table_column_foreign'
            $table->dropForeign(['desa_id']);
            
            // Tambahkan constraint baru referencing 'code'
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
        Schema::table('alamat', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);

            // Kembalikan ke 'id', meskipun salah, untuk menjaga konsistensi rollback
            $table->foreign('desa_id')
                ->references('id')
                ->on('indonesia_villages')
                ->onDelete('set null');
        });
    }
};
