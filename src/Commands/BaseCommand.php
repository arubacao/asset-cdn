<?php

/**
 * Created by PhpStorm.
 * User: Christopher
 * Date: 01.03.2018
 * Time: 18:51.
 */

namespace Arubacao\AssetCdn\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\SplFileInfo;

abstract class BaseCommand extends Command
{
    /**
     * @param  SplFileInfo[]  $files
     */
    protected function mapToPathname(array $files): array
    {
        return array_map(function (SplFileInfo $file) {
            return $file->getRelativePathname();
        }, $files);
    }
}
