<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE book_pages 
            ADD COLUMN search_vector_en TSVECTOR GENERATED ALWAYS AS (
                to_tsvector('english', content)
            ) STORED,
            ADD COLUMN search_vector_es TSVECTOR GENERATED ALWAYS AS (
                to_tsvector('spanish', content)
            ) STORED;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE book_pages 
            DROP COLUMN search_vector_en,
            DROP COLUMN search_vector_es;");
    }
};
