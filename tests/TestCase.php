<?php

namespace Arubacao\AssetCdn\Test;

use Arubacao\AssetCdn\AssetCdnServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\TemporaryDirectory\TemporaryDirectory;

abstract class TestCase extends Orchestra
{
    /** @var TemporaryDirectory */
    protected $tempDir;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->tempDir = (new TemporaryDirectory)->create();
        parent::setUp();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        $this->tempDir->delete();
        parent::tearDown();
    }

    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AssetCdnServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        //        $app['config']->set('filesystems.disks.public', [
        //            'driver' => 'local',
        //            'root' => $this->getMediaDirectory(),
        //        ]);
        $app['config']->set('app.key', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
        $app['config']->set('filesystems.disks.test_filesystem', [
            'driver' => 'local',
            'root' => $this->tempDir->path(),
        ]);

        $app['config']->set('asset-cdn.use_cdn', true);
        $app['config']->set('asset-cdn.cdn_url', 'http://cdn.localhost');
        $app['config']->set('asset-cdn.filesystem.disk', 'test_filesystem');

        $this->setApplicationPublicPath($app, __DIR__.'/testfiles/public');
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
            'cdn_url' => 'http://cdn.localhost',
            'filesystem' => [
                'disk' => 'test_filesystem',
                'options' => [],
            ],
            'files' => $files,
        ];

        $this->app->make('config')->set('asset-cdn', $result);
    }

    protected function setApplicationPublicPath($app, string $path): void
    {
        if (method_exists($app, 'usePublicPath')) {
            $app->usePublicPath($path);
        }

        $app->instance('path.public', $path);
    }
}
