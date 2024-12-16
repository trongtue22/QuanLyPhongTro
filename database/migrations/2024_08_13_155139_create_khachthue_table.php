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
        Schema::create('khachthue', function (Blueprint $table) {
            $table->id();
            // FK tham chiếu đến chutro
            $table->foreignId('chutro_id')->constrained('chutro')->onDelete('cascade');
            
            $table->string('tenkhachthue');
            $table->string('sodienthoai');
            $table->date('ngaysinh');
            $table->string('cccd');
            $table->boolean('gioitinh');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khachthue');
    }
};
