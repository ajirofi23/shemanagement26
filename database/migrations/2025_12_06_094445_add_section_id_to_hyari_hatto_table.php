<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_hyari_hatto', function (Blueprint $table) {
            // Asumsi tb_user memiliki kolom 'section_id'
            // Kita gunakan kolom foreign key yang merujuk ke tabel 'tb_sections' atau sejenisnya
            // Jika merujuk ke tabel users, gunakan: $table->foreignId('section_id')->constrained('tb_users', 'section_id'); 
            // Kita asumsikan section_id merujuk ke tabel master sections atau sekadar integer biasa
            $table->unsignedBigInteger('section_id')->nullable()->after('rekomendasi');
            
            // Opsional: Jika Anda memiliki tabel master sections yang terpisah
            // $table->foreign('section_id')->references('id')->on('tb_master_sections')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tb_hyari_hatto', function (Blueprint $table) {
            // Opsional: Hapus foreign key jika ada
            // $table->dropForeign(['section_id']); 
            
            $table->dropColumn('section_id');
        });
    }
};
