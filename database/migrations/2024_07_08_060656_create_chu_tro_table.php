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
        Schema::create('ChuTro', function (Blueprint $table) 
        {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('hinh_anh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ChuTro');
    }
};
