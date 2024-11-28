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
            $table->integer('secret')->comment("金鑰");
            $table->timestamps();
            $table->engine('InnoDB');
        });

        Schema::create('share_table_virtual_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_table_id')->constrained()->onDelete('cascade');
            $table->foreignId('virtual_file_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_table_virtual_file');
        Schema::dropIfExists('share_tables');
    }
};
