<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_riding_pelanggaran_fisik', function (Blueprint $table) {
            // Kolom ini menghubungkan SafetyRiding dengan PelanggaranFisik
            $table->foreignId('safety_riding_id')->constrained('safety_ridings')->onDelete('cascade');
            $table->foreignId('pf_id')->constrained('tb_master_pf')->onDelete('cascade');
            
            // Definisikan primary key gabungan
            $table->primary(['safety_riding_id', 'pf_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_riding_pelanggaran_fisik');
    }
};