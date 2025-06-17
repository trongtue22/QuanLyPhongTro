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
        Schema::table('khachthue_phongtro', function (Blueprint $table) {
            $table->string('trangthai')->nullable()->default('dang_o');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khachthue_phongtro', function (Blueprint $table) 
        {
           $table->dropColumn('trangthai');
        });
    }
};
