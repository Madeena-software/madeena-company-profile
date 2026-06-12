<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('content_json')->nullable();
            $table->string('content_language')->default('id');
            $table->boolean('enable_auto_numbering')->default(true);

            if (Schema::hasColumn('pages', 'content')) {
                $table->dropColumn('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->longText('content')->nullable();

            $table->dropColumn([
                'content_json',
                'content_language',
                'enable_auto_numbering',
            ]);
        });
    }
};
