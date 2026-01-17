<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_ridings', function (Blueprint $table) {
            $table->id();
            // Kunci Asing ke tabel users dan sections
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');

            $table->dateTime('waktu_kejadian');
            $table->string('type_kendaraan', 100);
            $table->string('nopol', 15);
            $table->text('keterangan_pelanggaran');
            $table->integer('total_pelanggaran')->default(0);
            $table->string('bukti')->nullable();
            $table->enum('status', ['Open', 'Close'])->default('Open');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_ridings');
    }
};