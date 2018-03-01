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
                ]
            ]
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
    public function command_syncs_files_with_different_modified_timestamps()
    {
        $time = Carbon::createFromFormat('Y-m-d', '2017-01-01')->timestamp;

        $this->seedLocalCdnFilesystem([
            [
                'path' => 'css',
                'filename' => 'front.css',
                'last_modified' => $time,
            ]
        ]);

        $this->setFilesInConfig([
            'include' => [
                'files' => [
                    'css/front.css',
                ]
            ]
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

}