<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_hyarihatto_pta', function (Blueprint $table) {
            $table->id(); 
            
            // Foreign Key ke Laporan Hyari Hatto (Definisi Eksplisit)
            $table->unsignedBigInteger('hyari_hatto_id'); 
            $table->foreign('hyari_hatto_id')
                  ->references('id')->on('tb_hyari_hatto')
                  ->onDelete('cascade');
            
            // Foreign Key ke Master Perilaku Tidak Aman
            $table->unsignedBigInteger('pta_id'); 
            $table->foreign('pta_id')->references('id')->on('tb_master_pta')->onDelete('cascade');
            
            $table->timestamps(); 

            // Memastikan tidak ada duplikasi entri
            $table->unique(['hyari_hatto_id', 'pta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_hyarihatto_pta');
    }
};