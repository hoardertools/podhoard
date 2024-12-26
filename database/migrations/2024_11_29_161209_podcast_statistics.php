<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            $table->biginteger('total_playtime')->default(0);
            $table->timestamp('latest_addition_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            //
        });
    }
};
