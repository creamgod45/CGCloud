<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 移除現有的外鍵約束
        Schema::table('dash_videos', function (Blueprint $table) {
            $table->dropForeign('dash_videos_thumb_virtual_file_uuid_foreign');
        });

        // 確保下一個 down 方法可以還原此更改
    }

    public function down(): void
    {
        // 如果需要還原，重新添加外鍵約束
        // 既然我們已經在 create_dash_videos_table 遷移中修改了 thumb_virtual_file_uuid 為軟外鍵
        // 所以這個遷移文件不再需要進行外鍵約束的移除操作
        // 此遷移可用於其他表結構調整或者保留為將來的更改

        // 修改 videoStream 和 audioStream 確保它們是 text 類型
        Schema::table('dash_videos', function (Blueprint $table) {
            if (Schema::hasColumn('dash_videos', 'videoStream') &&
                Schema::getColumnType('dash_videos', 'videoStream') === 'string') {
                $table->text('videoStream')->nullable()->comment('視頻流的詳細資料，包括幀率、編碼格式、解析度等')->change();
            }

            if (Schema::hasColumn('dash_videos', 'audioStream') &&
                Schema::getColumnType('dash_videos', 'audioStream') === 'string') {
                $table->text('audioStream')->nullable()->comment('音頻流的詳細資料，如編解碼格式、音訊通道等')->change();
            }
        });
    }
};
