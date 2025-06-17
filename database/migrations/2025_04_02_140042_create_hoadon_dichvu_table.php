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
        Schema::create('hoadon_dichvu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoadon_id')->constrained('hoadon')->onDelete('cascade');
            $table->foreignId('dichvu_id')->constrained('dichvu')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoadon_dichvu');
    }
};
