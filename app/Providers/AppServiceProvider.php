<?php

namespace App\Providers;

use Aws\AutoScaling\AutoScalingClient;
use Aws\Ec2\Ec2Client;
use Aws\ElasticLoadBalancing\ElasticLoadBalancingClient;
use Aws\S3\S3Client;
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
        $this->app->bind(S3Client::class, function ($app, $args) {
            return new S3Client(['region' => $args['region']]);
        });
        $this->app->bind(AutoScalingClient::class, function ($app, $args) {
            return new AutoScalingClient(['region' => $args['region']]);
        });
    }
}
