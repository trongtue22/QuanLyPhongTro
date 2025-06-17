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
        Schema::create('quanly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chutro_id')->constrained('ChuTro')->onDelete('cascade'); 
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('sodienthoai');
            $table->boolean('gioitinh');
            $table->string('mat_khau');
            $table->string('cccd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quanly');
    }
};
