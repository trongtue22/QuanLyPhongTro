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
        Schema::create('phongtro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daytro_id')->constrained('daytro')->onDelete('cascade'); // Giả sử bảng DayTro có tên là day_tro

            // Số phòng -> Ko dc trùng lặp => Có thể đổi thành string nếu cần sau này s 
            $table->integer('sophong')->unique();
           
            // Các trường thông tin khác 
            $table->decimal('tienphong', 10, 2);
            // Chứa thông tin phong trọ "đã thuê" hay "Phòng Trống" => Mặc định: phòng trống 
            $table->boolean('status')->default(false);

            $table->timestamps();



          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phongtro');
    }
};
