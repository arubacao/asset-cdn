<?php

namespace Arubacao\AssetCdn;

use Illuminate\Support\ServiceProvider;

class AssetCdnServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/asset-cdn.php' => config_path('asset-cdn.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/asset-cdn.php', 'asset-cdn');

        $this->app->singleton(Finder::class, function ($app) {
            return new Finder(new Config($app));
        });
    }
}