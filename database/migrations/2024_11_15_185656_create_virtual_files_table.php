<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('virtual_files', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->enum('type', ['temporary', 'persistent']);
            $table->string('filename')->index('filename');
            $table->string('path');
            $table->string('extension');
            $table->string('minetypes');
            $table->string('disk');
            $table->integer('expires_at');
            $table->timestamps();
            $table->engine('InnoDB');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_files');
    }
};
