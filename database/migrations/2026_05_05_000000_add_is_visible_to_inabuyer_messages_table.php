<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inabuyer_messages', function (Blueprint $table): void {
            $table->boolean('is_visible')->default(true)->after('kesan_dan_pesan');
        });
    }

    public function down(): void
    {
        Schema::table('inabuyer_messages', function (Blueprint $table): void {
            $table->dropColumn('is_visible');
        });
    }
};