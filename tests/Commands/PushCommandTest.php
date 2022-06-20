<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class PushCommandTest extends TestCase
{
    /** @test */
    public function command_pushes_all_js_paths_to_cdn()
    {
        $this->setFilesInConfig([
            'include' => [
                'paths' => [
                    'js',
                ],
            ],
        ]);

        $expectedFiles = [
            'js/back.app.js',
            'js/front.app.js',
            'vendor/horizon/js/app.js',
            'vendor/horizon/js/app.js.map',
        ];

        Artisan::call('asset-cdn:push');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);
    }

    /** @test */
    public function command_receives_options()
    {
        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'css/front.css',
                ],
            ],
        ]);

        $expectedOptions = [
            'foo' => 'bar',
        ];

        $this->app['config']->set('asset-cdn.filesystem.options', $expectedOptions);

        Storage::shouldReceive('disk->putFileAs')
            ->once()
            ->withArgs(
                function (
                    string $path,
                    \Illuminate\Http\File $file,
                    string $name,
                    array $options
                ) use ($expectedOptions) {
                    $this->assertEquals($expectedOptions, $options);

                    return true;
                }
            )
            ->andReturn('"css/front.css"');

        Artisan::call('asset-cdn:push');
    }
}
