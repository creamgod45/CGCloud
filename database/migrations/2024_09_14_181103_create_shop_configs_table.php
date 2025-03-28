<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('value');
            $table->timestamps();
            $table->engine('InnoDB');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_configs');
    }
};
