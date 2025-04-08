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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->string('facebook_link', 255)->nullable();
            $table->string('instagram_link', 255)->nullable();
            $table->string('twitter_link', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
