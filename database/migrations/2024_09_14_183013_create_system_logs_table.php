<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'Job:run.success',
                'Job:run.failed',
                'Queue:run.success',
                'Queue:run.failed',
                'Schedule:run.success',
                'Schedule:run.failed',
                'auth:Login',
                'auth:Logout',
                'auth:LogoutAllDevices',
                'auth:Kick',
                'auth:Ban',
                'Inventory:Add',
                'Inventory:Remove',
                'Inventory:Edit',
                'Member:Add',
                'Member:Remove',
                'Member:Edit',
                'ShopConfig:Add',
                'ShopConfig:Remove',
                'ShopConfig:Edit',
            ])->comment('Log 類型');
            $table->string('title')->comment('紀錄標題');
            $table->string('description')->comment('紀錄說明(非必填)')->nullable()->default('null');
            $table->string('UUID')->comment('操作會員 ID')->nullable()->default('null');
            $table->string('FingerPrint')->comment('操作瀏覽器的身分證')->nullable()->default('null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
