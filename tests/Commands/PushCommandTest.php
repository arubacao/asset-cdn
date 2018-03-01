<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\Artisan;

class PushCommandTest extends TestCase
{
    /** @test */
    public function command_pushes_all_js_paths_to_cdn()
    {
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

        Artisan::call('asset-cdn:push');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);
    }
}