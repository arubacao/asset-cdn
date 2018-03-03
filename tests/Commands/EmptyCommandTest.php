<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\Artisan;

class EmptyCommandTest extends TestCase
{
    /** @test */
    public function command_deletes_all_files_on_cdn()
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

        $expectedFiles = [];

        Artisan::call('asset-cdn:empty');

        $this->assertFilesExistOnCdnFilesystem($expectedFiles);
    }
}
