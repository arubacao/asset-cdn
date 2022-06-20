<?php

namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestCase extends \Arubacao\AssetCdn\Test\TestCase
{
    /**
     * @param  array  $expectedFiles
     */
    protected function assertFilesExistOnCdnFilesystem($expectedFiles)
    {
        // Assert the file was stored...
        foreach ($expectedFiles as $expectedFile) {
            Storage::disk('test_filesystem')->assertExists($expectedFile);
        }

        /** @var \Symfony\Component\Finder\SplFileInfo[] $actualFiles */
        $actualFiles = File::allFiles(config('filesystems.disks.test_filesystem.root'));
        $actualFiles = array_map(function ($file) {
            return $file->getRelativePathname();
        }, $actualFiles);

        // Sort the arrays
        asort($actualFiles);
        asort($expectedFiles);
        $actualFiles = array_values($actualFiles);
        $expectedFiles = array_values($expectedFiles);

        $this->assertEquals($expectedFiles, $actualFiles);
        $this->assertCount(count($expectedFiles), $actualFiles);
    }

    /**
     * @param  array  $files
     */
    protected function seedCdnFilesystem($files)
    {
        foreach ($files as $file) {
            $srcPath = $file['base'] ?? public_path();
            $source = "{$srcPath}/{$file['path']}/{$file['filename']}";

            Storage::disk('test_filesystem')
                ->putFileAs(
                    $file['path'],
                    new \Illuminate\Http\File($source),
                    $file['filename']
                );
        }
    }
}
