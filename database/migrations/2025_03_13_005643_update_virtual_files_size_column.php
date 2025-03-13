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
        //
        Schema::table('virtual_files', function (Blueprint $table) {
            $table->unsignedBigInteger('size')->change(); // 將 size 欄位改為 UNSIGNED BIGINT
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('virtual_files', function (Blueprint $table) {
            $table->integer('size')->change(); // 復原數據類型為 INT（如果需要）
        });
    }
};
