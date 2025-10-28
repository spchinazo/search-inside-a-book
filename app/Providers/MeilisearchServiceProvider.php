<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MeiliSearch\Client;

class MeilisearchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->runningInConsole() || $this->app->runningUnitTests()) {
            return;
        }

        try {
            $client = app(Client::class);
            $index = $client->index('book_pages');

            // Ranking rules otimizadas
            $index->updateRankingRules([
                'words',
                'typo',
                'proximity',
                'attribute',
                'sort',
                'exactness',
            ]);

            // Atributes searchable with weights
            $index->updateSearchableAttributes([
                'text_content',
            ]);

            // Atributes for filters
            $index->updateFilterableAttributes([
                'book_id',
            ]);

            // Atributes for sorting (future)
            $index->updateSortableAttributes([
                'page_number',
            ]);

        } catch (\Exception $e) {
            // Silent fail in environments without Meilisearch
            report($e);
        }
    }
}