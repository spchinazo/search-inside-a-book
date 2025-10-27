<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is set to null, Horizon will reside at the root of your
    | application's domain.
    |
    */

    'domain' => env('HORIZON_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. You are free
    | to change this to anything you like.
    |
    */

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection configuration to utilize when
    | communicating with Redis. This connection is used to store all of the
    | Horizon supervisors, failed jobs, statistics, and workers.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This is the prefix used to store all of Horizon's data in Redis. You
    | may modify this value for each application.
    |
    */

    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Queue Load Balancing
    |--------------------------------------------------------------------------
    |
    | This option controls how jobs are dispatched to your workers. "simple"
    | is the default; however, you may utilize the "auto" balancer for more
    | intelligent auto-scaling. You may also disable all balancing and manage
    | your own worker load.
    |
    */

    'balance' => 'auto',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | The following options configure the authentication / authorization
    | guards for the Horizon dashboard. These options are defined in your
    | application's "auth" configuration file.
    |
    */

    'guards' => [
        'web',
    ],

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming
    |--------------------------------------------------------------------------
    |
    | The following options configure the amount of days to retain job
    | records. This allows you to scale down the storage requirements
    | of the database and Redis store. You can also disable trimming
    | completely by setting this value to zero.
    |
    */

    'trim' => [
        'recent_jobs' => 7,
        'recent_failed' => 7,
        'monitored_jobs' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    |
    | The following array lists the jobs that should not be displayed on the
    | Horizon dashboard. This is useful for jobs that run every few seconds
    | and clutter the dashboard.
    |
    */

    'silenced' => [
        // Adicione aqui qualquer Job de indexação que queira ocultar
    ],

    /*
    |--------------------------------------------------------------------------
    | Environments
    |--------------------------------------------------------------------------
    |
    | In this section, you define the queue workers that will run during
    | each environment your application is operating in.
    |
    */

    'environments' => [
        'production' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'], // Fila principal para indexação Scout e outros Jobs
                'balance' => 'auto',
                'max_processes' => 10, // Máximo de workers que podem ser criados
                'min_processes' => 1, // Mínimo de workers sempre ativos
                'tries' => 3,
                'timeout' => 120, // Tempo limite (em segundos) para um Job
            ],
        ],

        'local' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'max_processes' => 3,
                'tries' => 1,
                'timeout' => 60,
            ],
        ],
    ],
];
