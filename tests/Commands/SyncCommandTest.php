<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SyncCommandTest extends TestCase
{
    /** @test */
    public function command_syncs_all_js_paths_and_deletes_css_files_to_cdn()
    {
        $this->seedCdnFilesystem([
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
    public function command_does_not_sync_identical_sync_files()
    {
        $this->seedCdnFilesystem([
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

        $modifiedBeforeSync = Storage::disk('test_filesystem')
            ->lastModified('css/front.css');

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);

        $modifiedAfterSync = Storage::disk('test_filesystem')
            ->lastModified('css/front.css');

        $this->assertEquals($modifiedBeforeSync, $modifiedAfterSync);
    }

    /** @test */
    public function command_syncs_files_with_different_size()
    {
        $src = public_path('css/front.css');
        $expectedFileSize = filesize($src);

        $this->seedCdnFilesystem([
            [
                'path' => 'css',
                'filename' => 'front.css',
                'base' => __DIR__.'/../testfiles/dummy',
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
    public function command_syncs_js_file_with_same_size_but_different_hash()
    {
        $src = public_path('js/front.app.js');
        $expectedHash = md5_file($src);

        $this->seedCdnFilesystem([
            [
                'path' => 'js',
                'filename' => 'front.app.js',
                'base' => __DIR__.'/../testfiles/dummy',
            ],
        ]);

        $this->assertNotEquals($expectedHash,
            md5(Storage::disk('test_filesystem')
                ->get('js/front.app.js'))
        );

        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'js/front.app.js',
                ],
            ],
        ]);

        $expectedFiles = [
            'js/front.app.js',
        ];

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);

        $this->assertEquals($expectedHash,
            md5(Storage::disk('test_filesystem')
                ->get('js/front.app.js'))
        );
    }

    /** @test */
    public function command_syncs_img_file_with_same_size_but_different_hash()
    {
        $src = public_path('img/layout/ph3x2.png');
        $dummySrc = __DIR__.'/../testfiles/dummy/img/layout/ph3x2.png';
        $expectedHash = md5_file($src);
        $dummyHash = md5_file($dummySrc);

        $this->assertEquals(filesize($src), filesize($dummySrc));
        $this->assertNotEquals($expectedHash, $dummyHash);

        $this->seedCdnFilesystem([
            [
                'path' => 'img/layout',
                'filename' => 'ph3x2.png',
                'base' => __DIR__.'/../testfiles/dummy',
            ],
        ]);

        $this->assertNotEquals($expectedHash,
            md5(Storage::disk('test_filesystem')
                ->get('img/layout/ph3x2.png'))
        );

        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'img/layout/ph3x2.png',
                ],
            ],
        ]);

        $expectedFiles = [
            'img/layout/ph3x2.png',
        ];

        Artisan::call('asset-cdn:sync');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);

        $this->assertEquals($expectedHash,
            md5(Storage::disk('test_filesystem')
                ->get('img/layout/ph3x2.png'))
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
                    $this->assertEquals($expectedOptions, $options);

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
