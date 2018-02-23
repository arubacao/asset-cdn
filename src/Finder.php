<?php

namespace Arubacao\AssetCdn;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder
{
    /**
     * The config repository instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles()
    {
        $pathFinder = $this->excluded();
        $nameFinder = clone $pathFinder;

        $includedPaths = $this->includedPaths($pathFinder);
        $includedNames = $this->includedNames($nameFinder);

        return $this->mergeFileInfos($includedPaths, $includedNames);
    }

    private function getBaseFinder(): SymfonyFinder
    {
        return SymfonyFinder::create()
            ->files()
            ->in($this->config->getPublicPath())
            ->ignoreDotFiles($this->config->ignoreDotFiles())
            ->ignoreVCS($this->config->ignoreVCS());
    }

    /**
     * @param \Symfony\Component\Finder\Finder $pathFinder
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    private function includedPaths(SymfonyFinder $pathFinder): array
    {
        /**
         * Include directories
         * @see http://symfony.com/doc/current/components/finder.html#location
         */
        $includedPaths = $this->config->getIncludedPaths();
        foreach ($includedPaths as $path) {
            $pathFinder->path($path);
        }

        /**
         * Include Files
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        $includedFiles = $this->config->getIncludedFiles();
        foreach ($includedFiles as $file) {
            $pathFinder->path($file);
        }

        if(empty($includedPaths) && empty($includedFiles)) {
            $pathFinder->notPath('');
        }

        return iterator_to_array(
            $pathFinder,
            false
        );
    }


    /**
     * @param \Symfony\Component\Finder\Finder $nameFinder
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    private function includedNames(SymfonyFinder $nameFinder): array
    {
        /**
         * Include Extensions
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        $includedExtensions = $this->config->getIncludedExtensions();
        foreach ($includedExtensions as $extension) {
            $nameFinder->name($extension);
        }

        /**
         * Include Patterns - globs, strings, or regexes
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        $includedPatterns = $this->config->getIncludedPatterns();
        foreach ($includedPatterns as $pattern) {
            $nameFinder->name($pattern);
        }

        if(empty($includedExtensions) && empty($includedPatterns)) {
            $nameFinder->notPath('');
        }

        return iterator_to_array(
            $nameFinder,
            false
        );
    }

    private function excluded()
    {
        $finder = $this->getBaseFinder();

        /**
         * Exclude directories
         * @see http://symfony.com/doc/current/components/finder.html#location
         */
        $finder->exclude($this->config->getExcludedPaths());

        /**
         * Exclude Files
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        foreach ($this->config->getExcludedFiles() as $file) {
            $finder->notPath($file);
        }

        /**
         * Exclude Extensions
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        foreach ($this->config->getExcludedExtensions() as $pattern) {
            $finder->notName($pattern);
        }

        /**
         * Exclude Patterns - globs, strings, or regexes
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        foreach ($this->config->getExcludedPatterns() as $pattern) {
            $finder->name($pattern);
        }

        return $finder;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo[] $includedPaths
     * @param \Symfony\Component\Finder\SplFileInfo[] $includedNames
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    private function mergeFileInfos(array $includedPaths, array $includedNames): array
    {
        return collect(array_merge($includedPaths, $includedNames))
            ->unique(function (SplFileInfo $file) {
                return $file->getPathname();
            })
            ->values()
            ->toArray();
    }
}