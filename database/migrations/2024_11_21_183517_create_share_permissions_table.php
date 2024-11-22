<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_tables_id')->references('id')->on('share_tables')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members');
            $table->string('permission_type');
            $table->string('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_permissions');
    }
};
