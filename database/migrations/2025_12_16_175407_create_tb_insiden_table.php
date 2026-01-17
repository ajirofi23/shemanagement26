<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('tb_insiden')) {
            Schema::create('tb_insiden', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->time('jam');
                $table->string('lokasi', 150);
                $table->string('kategori', 100);
                $table->string('work_accident_type', 50)->nullable();
                $table->string('departemen', 100);
                $table->unsignedBigInteger('section_id')->nullable();
                $table->string('kondisi_luka', 200)->nullable();
                $table->text('kronologi')->nullable();
                $table->text('keterangan_lain')->nullable();
                $table->json('foto')->nullable();
                $table->enum('status', ['open', 'progress', 'closed'])->default('open');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_insiden');
    }
};