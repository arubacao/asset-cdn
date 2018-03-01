<?php
namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestCase extends \Arubacao\AssetCdn\Test\TestCase
{
    /**
     * @param array $expectedFiles
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

        $this->assertArraySubset($expectedFiles, $actualFiles);
        $this->assertCount(count($expectedFiles), $actualFiles);
    }

    /**
     * @param array $files
     */
    protected function seedLocalCdnFilesystem($files)
    {
        $cdnPath = config('filesystems.disks.test_filesystem.root');

        foreach ($files as $file) {
            if (!file_exists("{$cdnPath}/{$file['path']}")) {
                mkdir("{$cdnPath}/{$file['path']}", 0777, true);
            }
            $source = public_path("{$file['path']}/{$file['filename']}");
            $dest = "{$cdnPath}/{$file['path']}/{$file['filename']}";
            copy($source, $dest);
            // Preserve modified timestamp of original file
            touch($dest, filemtime($source));
        }
    }

    /**
     * @param array $files
     */
    protected function seedCdnFilesystem($files)
    {
        foreach ($files as $file) {
            Storage::disk('test_filesystem')
                ->putFileAs(
                    $file['path'],
                    new \Illuminate\Http\File(public_path("{$file['path']}/{$file['filename']}")),
                    $file['filename']
                );
        }
    }
}