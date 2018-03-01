<?php
namespace Arubacao\AssetCdn\Test\Commands;

use Illuminate\Support\Facades\File;

class TestCase extends \Arubacao\AssetCdn\Test\TestCase
{
    /**
     * @param array $expectedFiles
     */
    protected function assertFilesExistOnCdnFilesystem($expectedFiles)
    {
        /** @var \Symfony\Component\Finder\SplFileInfo[] $actualFiles */
        $actualFiles = File::allFiles(config('filesystems.disks.test_filesystem.root'));
        $actualFiles = array_map(function ($file) {
            return $file->getRelativePathname();
        }, $actualFiles);

        $this->assertArraySubset($expectedFiles, $actualFiles);
        $this->assertCount(count($expectedFiles), $actualFiles);
    }
}