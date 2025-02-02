<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dash_videos',
            function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('virtual_file_uuid')->comment('關聯檔案')->constrained('virtual_files', 'uuid')->onDelete('cascade');
                $table->foreignId('share_table_virtual_file_id')->comment('關聯分享資源中間表')->constrained('share_table_virtual_file')->onDelete('cascade');
                $table->foreignId('member_id')->comment('關聯擁有者')->constrained()->onDelete('cascade');
                $table->foreignUuid('thumb_virtual_file_uuid')->nullable()->comment('關聯封面照片')->constrained('virtual_files', 'uuid');
                $table->text('path')->nullable()->comment('mpd 路徑');
                $table->string('filename')->nullable()->comment('mpd 檔案名稱');
                $table->string('extension')->nullable()->comment('mpd 副檔案名');
                $table->string('disk')->nullable()->comment('mpd 存在位置');
                $table->integer('size')->nullable()->comment('mpd 檔案大小');
                $table->enum('type', ['wait', 'processing', 'success', 'failed'])->default('wait')->comment('狀態');
                $table->string('format')->nullable()->comment("影像格式");
                $table->string('audioCodec')->nullable()->comment('音訊編解碼格式');
                $table->string('videoCodec')->nullable()->comment('視訊編解碼格式');
                $table->integer('width')->nullable()->comment('寬度');
                $table->integer('height')->nullable()->comment('高度');
                $table->string('framerate')->nullable()->comment('幀率');
                $table->string('bitrate')->nullable()->comment('比特率');
                $table->integer('duration')->nullable()->comment('總時間(秒數)');
                $table->string('channels')->nullable()->comment('音訊的通道數量');
                $table->string('sampleRate')->nullable()->comment('音訊的採樣率');
                $table->string('videoFrames')->nullable()->comment('影片的總幀數');
                $table->text('metadata')->nullable()->comment('影片的元數據，包括標題、作者、日期等');
                $table->string('videoStream')->nullable()->comment('視頻流的詳細資料，包括幀率、編碼格式、解析度等');
                $table->string('audioStream')->nullable()->comment('音頻流的詳細資料，如編解碼格式、音訊通道等');
                $table->timestamps();
                $table->engine('InnoDB');
            });

        Schema::table('share_table_virtual_file', function (Blueprint $table) {
            $table->foreignId('dash_videos_id')->comment('關聯 dash_videos 表')->change()->constrained('dash_videos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dash_videos');
    }
};
