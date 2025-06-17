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
        Schema::create('hopdong', function (Blueprint $table) {
            $table->id();
            // FK của pivot table tham chiếu đến 2 bảng cha: khachthue và phongtro 
            $table->foreignId('khachthue_phongtro_id')->constrained('khachthue_phongtro')->onDelete('cascade');
            // Các thông tin phụ 
            $table->integer('songuoithue');
            $table->date('ngaybatdau');
            $table->date('ngayketthuc');
            $table->decimal('tiencoc', 10, 2);
            $table->integer('soxe');
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hopdong');
    }
};
