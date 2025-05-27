<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('share_permissions', function (Blueprint $table) {
            // 檢查欄位是否存在，如存在則先移除
            if (Schema::hasColumn('share_permissions', 'start_at')) {
                $table->dropColumn('start_at');
            }
            if (Schema::hasColumn('share_permissions', 'end_at')) {
                $table->dropColumn('end_at');
            }

            // 新增 integer 類型的欄位
            $table->integer('start_at')->default(0)->comment('開始時間（整數型態）');
            $table->integer('end_at')->default(0)->comment('結束時間（整數型態）');
        });
    }

    public function down(): void
    {
        Schema::table('share_permissions', function (Blueprint $table) {
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }
};
