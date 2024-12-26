<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            $table->string('rss_access_key')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            //
        });
    }
};
