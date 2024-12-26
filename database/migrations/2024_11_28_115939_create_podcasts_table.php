<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('imageUrl')->nullable();
            $table->string('rssUrl')->nullable();
            $table->dateTime('last_scanned_at')->nullable();
            $table->string('last_rss_scanned_at')->nullable();
            $table->string('path');
            $table->foreignId('directory_id');
            $table->foreignId('library_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
