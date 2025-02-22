<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('share_table_virtual_file', function (Blueprint $table) {
            $table->foreignId('dash_videos_id')->default(null)->nullable(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('share_table_virtual_file', function (Blueprint $table) {
            //
        });
    }
};
