<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;

class ConfigureMeilisearch extends Command
{
    protected $signature = 'meilisearch:configure';
    protected $description = 'Configure Meilisearch index settings for optimal search performance';

    public function handle(): int
    {
        $this->info('Configuring Meilisearch index settings...');

        try {
            $client = app(Client::class);
            $index = $client->index('book_pages');

            // Ranking rules optimized
            $this->info('Setting ranking rules...');
            $task = $index->updateRankingRules([
                'words',
                'typo',
                'proximity',
                'attribute',
                'sort',
                'exactness',
            ]);
            $client->waitForTask($task['taskUid'], 5000);

            // Atributes searchable
            $this->info('Setting searchable attributes...');
            $task = $index->updateSearchableAttributes([
                'text_content',
            ]);
            $client->waitForTask($task['taskUid'], 5000);

            // Atributes filterable
            $this->info('Setting filterable attributes...');
            $task = $index->updateFilterableAttributes([
                'book_id',
            ]);
            $client->waitForTask($task['taskUid'], 5000);

            // Atributes sortable
            $this->info('Setting sortable attributes...');
            $task = $index->updateSortableAttributes([
                'page_number',
            ]);
            $client->waitForTask($task['taskUid'], 5000);

            // Typo tolerance settings
            $this->info('Setting typo tolerance...');
            $task = $index->updateTypoTolerance([
                'enabled' => true,
                'minWordSizeForTypos' => [
                    'oneTypo' => 4,
                    'twoTypos' => 8,
                ],
            ]);
            $client->waitForTask($task['taskUid'], 5000);

            $this->info('Meilisearch configuration completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to configure Meilisearch: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}