<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['public', 'private'])->default('public');
            $table->integer('expired_at');
            $table->string('short_code')->comment("短連接代碼");
            $table->string('secret', 255)->comment("金鑰")->nullable();
            $table->timestamps();
            $table->engine('InnoDB');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_table_virtual_file');
        Schema::dropIfExists('share_tables');
    }
};
