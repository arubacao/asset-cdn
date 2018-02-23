<?php
namespace Arubacao\AssetCdn\Test\Finder;

use Arubacao\AssetCdn\Finder;

class TestCase extends \Arubacao\AssetCdn\Test\TestCase
{
    /**
     * @param array $expectedFiles
     * @param array $fileConfig
     */
    protected function assertFilesMatchConfig($expectedFiles, $fileConfig)
    {
        $this->setFilesInConfig($fileConfig);

        /** @var \Symfony\Component\Finder\SplFileInfo[] $actualFiles */
        $actualFiles = resolve(Finder::class)->getFiles();
        $actualFiles = array_map(function ($file) {
            return $file->getRelativePathname();
        }, $actualFiles);

//        dd($actualFiles);

        $this->assertArraySubset($expectedFiles, $actualFiles);
        $this->assertCount(count($expectedFiles), $actualFiles);
    }
}