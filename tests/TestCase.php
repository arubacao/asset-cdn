<?php

namespace Arubacao\AssetCdn\Test;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Arubacao\AssetCdn\AssetCdnServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
//        $app['config']->set('filesystems.disks.public', [
//            'driver' => 'local',
//            'root' => $this->getMediaDirectory(),
//        ]);
//        $app['config']->set('filesystems.disks.secondMediaDisk', [
//            'driver' => 'local',
//            'root' => $this->getTempDirectory('media2'),
//        ]);
        $app['config']->set('app.key', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
        $app->bind('path.public', function () {
            return __DIR__.'/public';
        });
    }

    protected function setFilesInConfig(array $config)
    {
        $emptyConfig = [
            'ignoreDotFiles' => true,
            'ignoreVCS' => true,
            'include' => [
                'paths' => [],
                'files' => [],
                'extensions' => [],
                'patterns' => [],
            ],
            'exclude' => [
                'paths' => [],
                'files' => [],
                'extensions' => [],
                'patterns' => [],
            ],
        ];

        $files = array_merge_recursive($emptyConfig, $config);
        $result = [
            'use_cdn' => true,
            'filesystem' => 'asset-cdn',
            'url' => 'http://cdn.localhost',
            'files' => $files
        ];

        $this->app->make('config')->set('asset-cdn', $result);
    }

}