<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->comment('Tabla que almacena las páginas de un libro con su contenido de texto para búsquedas full-text');
            $table->id();
            $table->integer('page_number')->unique()->comment('Número de página'); // Es unique porque tengo un solo libro, en un sistema con varios libros no lo seria
            $table->text('text_content')->comment('Contenido de texto de la página para búsquedas full-text');
            $table->timestamps();

            // Index para ordenar resultados por página
            $table->index('page_number');
        });

        // Columna tsvector para búsquedas full-text
        // Esta es una columna computada que PostgreSQL mantendrá automáticamente
        DB::statement('ALTER TABLE pages ADD COLUMN text_search_vector tsvector');

        // Crear un índice GIN para búsqueda full-text rápida
        // GIN (Generalized Inverted Index) está optimizado para columnas tsvector
        DB::statement('CREATE INDEX pages_text_search_idx ON pages USING GIN(text_search_vector)');

        // Crear un trigger para actualizar automáticamente la columna tsvector cuando se cambie el texto
        // Esto mantiene el índice actualizado sin intervención manual
        DB::statement("
    CREATE TRIGGER pages_text_search_update BEFORE INSERT OR UPDATE
    ON pages FOR EACH ROW EXECUTE FUNCTION
    tsvector_update_trigger(text_search_vector,'pg_catalog.english',text_content)
");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS pages_text_search_update ON pages');
        Schema::dropIfExists('pages');
    }
};
