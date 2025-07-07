<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->string('name', 100);
            $table->string('password', 200);
            $table->date('birthdate', 200);
            $table->string('city', 200)->nullable();
            $table->string('work', 200)->nullable();
            $table->string('avatar', 100)->default('default.jpg');
            $table->string('cover', 100)->default('default.jpg');
            $table->string('token', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
