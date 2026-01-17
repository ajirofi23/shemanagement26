<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_riding_pelanggaran_dokumen', function (Blueprint $table) {
            // Kolom ini menghubungkan SafetyRiding dengan PelanggaranDokumen
            $table->foreignId('safety_riding_id')->constrained('safety_ridings')->onDelete('cascade');
            $table->foreignId('pd_id')->constrained('tb_master_pd')->onDelete('cascade');
            
            // Definisikan primary key gabungan untuk mencegah duplikasi
            $table->primary(['safety_riding_id', 'pd_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_riding_pelanggaran_dokumen');
    }
};