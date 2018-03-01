<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\Artisan;

class SyncCommandTest extends TestCase
{
    /** @test */
    public function command_syncs_all_js_paths_to_cdn()
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
}