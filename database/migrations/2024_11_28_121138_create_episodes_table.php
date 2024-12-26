<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('imageUrl')->nullable();
            $table->string('published_at')->nullable();
            $table->foreignId('library_id');
            $table->foreignId('directory_id');
            $table->foreignId('podcast_id');
            $table->boolean('downloaded')->nullable();
            $table->dateTime('downloaded_at')->nullable();
            $table->boolean('metadata_set');
            $table->integer('season')->nullable();
            $table->integer('episode')->nullable();
            $table->string('path', 2048);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
