<?php

namespace Arubacao\AssetCdn\Commands;

use Arubacao\AssetCdn\Finder;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\File;

class PushCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset-cdn:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pushes assets to CDN';

    /**
     * Execute the console command.
     *
     * @param Finder $finder
     * @param FilesystemManager $filesystemManager
     * @param Repository $config
     *
     * @return void
     */
    public function handle(Finder $finder, FilesystemManager $filesystemManager, Repository $config)
    {
        $files = $finder->getFiles();

        foreach ($files as $file) {
            $bool = $filesystemManager
                ->disk($config->get('asset-cdn.filesystem.disk'))
                ->putFileAs(
                    $file->getRelativePath(),
                    new File($file->getPathname()),
                    $file->getFilename(),
                    $config->get('asset-cdn.filesystem.options')
                );

            if (! $bool) {
                $this->error("Problem uploading: {$file->getRelativePathname()}");
            } else {
                $this->info("Successfully uploaded: {$file->getRelativePathname()}");
            }
        }
    }
}
