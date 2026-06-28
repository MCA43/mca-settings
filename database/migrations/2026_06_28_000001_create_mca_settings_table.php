<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('settings.table', 'mca_settings'), function (Blueprint $table) {
            $table->id();
            $table->string('group', 64)->default('general');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 32)->default('string');
            $table->json('label')->nullable();
            $table->json('description')->nullable();
            $table->unsignedSmallInteger('sort')->default(100);
            $table->boolean('is_locked')->default(true);
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('settings.table', 'mca_settings'));
    }
};
