<?php

namespace Arubacao\AssetCdn\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\TemporaryDirectory\TemporaryDirectory;

abstract class TestCase extends Orchestra
{
    /** @var \Spatie\TemporaryDirectory\TemporaryDirectory */
    protected $tempDir;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->tempDir = (new TemporaryDirectory())->create();
        parent::setUp();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->tempDir->delete();
        parent::tearDown();
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
        $app['config']->set('app.key', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
        $app['config']->set('asset-cdn.filesystem', 'test_filesystem');
        $app['config']->set('filesystems.disks.test_filesystem', [
            'driver' => 'local',
            'root' => $this->tempDir->path(),
        ]);
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
            'filesystem' => 'test_filesystem',
            'url' => 'http://cdn.localhost',
            'files' => $files
        ];

        $this->app->make('config')->set('asset-cdn', $result);
    }

}