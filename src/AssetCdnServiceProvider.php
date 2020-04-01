<?php

namespace Arubacao\AssetCdn;

use Arubacao\AssetCdn\Commands\EmptyCommand;
use Arubacao\AssetCdn\Commands\PushCommand;
use Arubacao\AssetCdn\Commands\SyncCommand;
use Illuminate\Support\ServiceProvider;

class AssetCdnServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
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
            return new Finder(new Config($app->make('config'), $app->make('path.public')));
        });

        $this->app->bind('command.asset-cdn:push', PushCommand::class);
        $this->app->bind('command.asset-cdn:sync', SyncCommand::class);
        $this->app->bind('command.asset-cdn:empty', EmptyCommand::class);

        $this->commands([
            'command.asset-cdn:push',
            'command.asset-cdn:sync',
            'command.asset-cdn:empty',
        ]);
    }
}
