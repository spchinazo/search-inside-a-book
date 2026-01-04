<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function withinTransaction(): bool
    {
        return false;
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_pages', function (Blueprint $table) {
            $table->index(['search_vector_en'], 'idx_pages_fts_en', 'gin');
            $table->index(['search_vector_es'], 'idx_pages_fts_es', 'gin');
            $table->index(['book_id', 'page_number'], 'idx_book_pages_book_page', 'btree');
            $table->index(['book_id'], 'idx_book_pages_book_id', 'btree');
            $table->index(['page_number'], 'idx_book_pages_brin_page', 'brin');
            $table->index(['book_id', 'page_number'], 'idx_book_pages_covering', 'btree');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_pages', function (Blueprint $table) {
            $table->dropIndex('idx_pages_fts_en');
            $table->dropIndex('idx_pages_fts_es');
        });
    }
};
