<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            // In development: only allow localhost access
            // In production: implement proper authentication
            $allowedIps = ['127.0.0.1', '::1'];
            
            if (app()->environment('local')) {
                return in_array(request()->ip(), $allowedIps);
            }
            
            // In production, you should check for authenticated admin users:
            // return optional($user)->isAdmin();
            
            return false; // Deny access by default in non-local environments
        });
    }
}