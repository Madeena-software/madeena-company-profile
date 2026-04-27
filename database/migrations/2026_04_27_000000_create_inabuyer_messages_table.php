<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inabuyer_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 255);
            $table->string('organization', 255)->nullable();
            $table->text('kesan_dan_pesan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inabuyer_messages');
    }
};