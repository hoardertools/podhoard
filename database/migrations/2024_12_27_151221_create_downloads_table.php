<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->dateTime('published_at');
            $table->foreignId('podcast_id');
            $table->foreignId('directory_id');
            $table->foreignId('library_id');
            $table->boolean('downloaded');
            $table->dateTime('downloaded_at');
            $table->string('path');
            $table->string('title');
            $table->bigInteger('duration');
            $table->bigInteger('filesize');
            $table->string('download_url');
            $table->string('guid');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
