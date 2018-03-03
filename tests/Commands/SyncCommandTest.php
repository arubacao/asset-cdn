<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SyncCommandTest extends TestCase
{
    /** @test */
    public function command_syncs_all_js_paths_and_deletes_css_files_to_cdn()
    {
        $this->seedLocalCdnFilesystem([
            [
                'path' => 'js',
                'filename' => 'back.app.js',
            ],
            [
                'path' => 'css',
                'filename' => 'front.css',
            ],
            [
                'path' => 'css',
                'filename' => 'back.css',
            ],
        ]);

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

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);
    }

    /** @test */
    public function command_syncs_files_with_different_modified_timestamps_but_same_size()
    {
        $time = Carbon::createFromFormat('Y-m-d', '2017-01-01')->timestamp;

        $this->seedLocalCdnFilesystem([
            [
                'path' => 'css',
                'filename' => 'front.css',
                'last_modified' => $time,
            ],
        ]);

        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'css/front.css',
                ],
            ],
        ]);

        $expectedFiles = [
            'css/front.css',
        ];

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);

        $modified = Storage::disk('test_filesystem')
            ->lastModified('css/front.css');

        $this->assertNotEquals($time, $modified);
    }

    /** @test */
    public function command_does_not_sync_files_with_same_timestamps_and_same_size()
    {
        $this->seedLocalCdnFilesystem([
            [
                'path' => 'css',
                'filename' => 'front.css',
            ],
        ]);

        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'css/front.css',
                ],
            ],
        ]);

        $expectedFiles = [
            'css/front.css',
        ];

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);

        $modified = Storage::disk('test_filesystem')
            ->lastModified('css/front.css');

        $this->assertEquals(filemtime(public_path('css/front.css')), $modified);
    }

    /** @test */
    public function command_syncs_files_with_same_modified_timestamps_but_different_size()
    {
        $src = public_path('css/front.css');
        $expectedFileSize = filesize($src);
        $expectedFileMTime = filemtime($src);

        $this->seedLocalCdnFilesystem([
            [
                'path' => 'css',
                'filename' => 'front.css',
                'base' => __DIR__.'/../testfiles/dummy',
                'last_modified' => $expectedFileMTime,
            ],
        ]);

        $this->assertNotEquals($expectedFileSize,
            Storage::disk('test_filesystem')
                ->size('css/front.css')
            );

        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'css/front.css',
                ],
            ],
        ]);

        $expectedFiles = [
            'css/front.css',
        ];

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);

        $this->assertEquals($expectedFileSize,
            Storage::disk('test_filesystem')
                ->size('css/front.css')
        );
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

        Storage::shouldReceive('disk->allFiles')
            ->once()
            ->andReturn([]);

        Storage::shouldReceive('disk->putFileAs')
            ->once()
            ->withArgs(
                function (
                    string $path,
                    \Illuminate\Http\File $file,
                    string $name,
                    array $options
                ) use ($expectedOptions) {
                    $this->assertArraySubset($expectedOptions, $options);

                    return true;
                }
            )
            ->andReturn('"css/front.css"');

        Storage::shouldReceive('disk->delete')
            ->once()
            ->with([])
            ->andReturn(true);

        Artisan::call('asset-cdn:sync');
    }
}
