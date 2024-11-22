<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->uuid("UUID")->unique();
            $table->string("username")->unique();
            $table->string("email")->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text("password");
            $table->string("phone", 255)->unique();
            $table->enum("enable", ["false", "true"])->default("false");
            $table->enum("administrator", ["false", "true"])->default("false");
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
