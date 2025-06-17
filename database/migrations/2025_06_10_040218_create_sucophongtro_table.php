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
        Schema::create('sucophongtro', function (Blueprint $table) {
            $table->id();

            // Liên kết đến phòng trọ
            $table->foreignId('phongtro_id')->constrained('phongtro')->onDelete('cascade');
            // Loại sự cố
            $table->string('loai_su_co');
            // Cho nhập vào 
            $table->text('mo_ta');

            $table->enum('trang_thai', ['chua_xu_ly', 'dang_xu_ly', 'da_xu_ly'])->default('chua_xu_ly');
            $table->json('goi_y_dich_vu')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucophongtro');
    }
};
