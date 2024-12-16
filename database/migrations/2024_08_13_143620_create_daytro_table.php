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
        Schema::create('daytro', function (Blueprint $table) {
            
            $table->id(); // id (PK) - Tự động tăng
            // FK tới bảng ChuTro
            $table->foreignId('chutro_id')->constrained('ChuTro')->onDelete('cascade'); 
            $table->foreignId('quanly_id')->nullable()->constrained('quanly')->onDelete('cascade'); 
            // Các trường thông tin khác 
            $table->string('tendaytro')->unique(); 
            $table->string('tinh');
            $table->string('huyen');
            $table->string('xa'); 
            $table->string('sonha'); 

            // Thời gian tạo tự cập nhật  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daytro');
    }
};
