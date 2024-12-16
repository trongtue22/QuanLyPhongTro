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
        Schema::create('dichvu', function (Blueprint $table) {
            $table->id();
            // FK tham chiếu đến chutro
            $table->foreignId('chutro_id')->constrained('chutro')->onDelete('cascade');

            $table->decimal('dien', 10, 2);  // Chi phí điện
            $table->decimal('nuoc', 10, 2);  // Chi phí nước
            $table->decimal('wifi', 10, 2);  // Chi phí wifi
            $table->decimal('guixe', 10, 2); // Chi phí gửi xe
            $table->decimal('rac', 10, 2);   // Chi phí rác
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dichvu');
    }
};
