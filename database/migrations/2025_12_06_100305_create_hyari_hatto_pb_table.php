<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_hyarihatto_pb', function (Blueprint $table) {
            $table->id(); 
            
            // Foreign Key ke Laporan Hyari Hatto (Definisi Eksplisit)
            $table->unsignedBigInteger('hyari_hatto_id'); 
            $table->foreign('hyari_hatto_id')
                  ->references('id')->on('tb_hyari_hatto')
                  ->onDelete('cascade');
            
            // Foreign Key ke Master Potensi Bahaya
            $table->unsignedBigInteger('pb_id'); 
            $table->foreign('pb_id')->references('id')->on('tb_master_pb')->onDelete('cascade');
            
            $table->timestamps(); 
            $table->unique(['hyari_hatto_id', 'pb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_hyarihatto_pb');
    }
};