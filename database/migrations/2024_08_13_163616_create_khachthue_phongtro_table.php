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
        Schema::create('khachthue_phongtro', function (Blueprint $table) 
        {
            $table->id();
            // FK tới 2 bảng: khacthue và phongtro    
            $table->foreignId('khachthue_id')->constrained('khachthue')->onDelete('cascade');
            $table->foreignId('phongtro_id')->constrained('phongtro')->onDelete('cascade'); 

            // Liên kết tới table dịch vụ
            $table->foreignId('dichvu_id')->nullable()->constrained('dichvu')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khachthue_phongtro');

    }
};
