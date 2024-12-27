<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn(['downloaded', 'downloaded_at']);
        });
    }

    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            //
        });
    }
};
