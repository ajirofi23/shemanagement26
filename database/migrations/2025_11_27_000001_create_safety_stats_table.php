<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('safety_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_safety_work_days')->default(0);
            $table->date('recorded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_stats');
    }
};