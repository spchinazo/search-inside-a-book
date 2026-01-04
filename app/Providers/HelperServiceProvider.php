<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $helperFolder = app_path('Helpers');
        if (File::exists($helperFolder)) {
            $helperFilesInFolder = File::files($helperFolder);
            foreach ($helperFilesInFolder as $helper) {
                require_once($helper->getPathname());
            }
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}