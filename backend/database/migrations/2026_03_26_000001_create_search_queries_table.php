<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('term')->unique();
            $table->unsignedBigInteger('times')->default(0);
            $table->unsignedBigInteger('hits_count')->default(0);
            $table->timestamp('last_used_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};
