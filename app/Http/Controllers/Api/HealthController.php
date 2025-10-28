<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use MeiliSearch\Client;

class HealthController extends Controller
{
    public function __invoke()
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'services' => [],
        ];

        // Check Database
        try {
            DB::connection()->getPdo();
            $health['services']['database'] = 'healthy';
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['services']['database'] = 'unhealthy';
        }

        // Check Redis/Cache
        try {
            Cache::set('health_check', true, 10);
            Cache::get('health_check');
            $health['services']['cache'] = 'healthy';
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['services']['cache'] = 'unhealthy';
        }

        // Check Meilisearch
        try {
            $client = app(Client::class);
            $client->health();
            $health['services']['meilisearch'] = 'healthy';
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['services']['meilisearch'] = 'unhealthy';
        }

        $statusCode = $health['status'] === 'healthy' ? 200 : 503;
        
        return response()->json($health, $statusCode);
    }
}