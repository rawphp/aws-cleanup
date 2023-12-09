<?php

namespace App\Providers;

use Aws\Ec2\Ec2Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Ec2Client::class, function ($app, $args) {
            return new Ec2Client(['region' => $args['region']]);
        });
    }
}
