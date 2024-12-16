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
        Schema::create('hoadon', function (Blueprint $table) {
            $table->id();
            // FK: khóa ngoại tham chiếu tới khachthue_phongtro
            // $table->foreignId('khachthue_phongtro_id')->constrained('khachthue_phongtro')->onDelete('cascade');

            // Tham chiếu khóa ngoại đến với table dich vu
            $table->foreignId('dichvu_id')->constrained('dichvu')->onDelete('cascade');
            $table->foreignId('hopdong_id')->constrained('hopdong')->onDelete('cascade');
            // Các thông tin khác 
            $table->integer('sodiencu');
            $table->integer('sodienmoi');
            $table->integer('sonuoccu');
            $table->integer('sonuocmoi');
            $table->decimal('tongtien', 10, 2);
            $table->boolean('status')->default(false); // Mặc định là chưa thanh toán 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoadon');
    }
};
