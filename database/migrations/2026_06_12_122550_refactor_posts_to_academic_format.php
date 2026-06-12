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
        Schema::table('posts', function (Blueprint $table) {
            $table->json('content_json')->nullable();
            $table->text('abstract')->nullable();
            $table->json('keywords')->nullable();
            $table->json('authors_info')->nullable();
            $table->string('content_language')->default('id');
            $table->boolean('enable_auto_numbering')->default(true);

            if (Schema::hasColumn('posts', 'body')) {
                $table->dropColumn('body');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->longText('body')->nullable();

            $table->dropColumn([
                'content_json',
                'abstract',
                'keywords',
                'authors_info',
                'content_language',
                'enable_auto_numbering',
            ]);
        });
    }
};
