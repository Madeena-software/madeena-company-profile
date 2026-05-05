<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inabuyer_messages', function (Blueprint $table): void {
            $table->string('position', 255)->nullable()->after('organization');
            $table->string('phone', 50)->nullable()->after('position');
            $table->string('email', 255)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('inabuyer_messages', function (Blueprint $table): void {
            $table->dropColumn(['position', 'phone', 'email']);
        });
    }
};
