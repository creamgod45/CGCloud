<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('share_table_virtual_file', function (Blueprint $table) {
            $table->foreign('dash_videos_id')
                ->references('id')
                ->on('dash_videos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('share_table_virtual_file', function (Blueprint $table) {
            //
        });
    }
};
