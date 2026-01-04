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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('lang', ['en', 'es', 'pt'])->default('en');
            $table->string('isbn')->unique();
            $table->string('path')->nullable();
            $table->string('disk')->nullable();
            $table->string('front')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['title'], 'idx_books_title', 'btree');
            $table->index(['isbn'], 'idx_books_isbn', 'btree');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
