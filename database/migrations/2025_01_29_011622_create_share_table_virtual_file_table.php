<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share_table_virtual_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_table_id')->constrained('share_tables')->onDelete('cascade');
            $table->foreignUuid('virtual_file_uuid')->constrained('virtual_files', 'uuid')->onDelete('cascade');
			$table->unsignedBigInteger('dash_videos_id')->default(null)->nullable(true)->comment('關聯 DashVideos 表');
            $table->timestamps();
            $table->engine('InnoDB');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_table_virtual_file');
    }
};
