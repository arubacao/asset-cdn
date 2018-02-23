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
        $this->finder = SymfonyFinder::create()
            ->files()
            ->in($this->config->getPublicPath())
        ;
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles()
    {
        return $this->includedFiles();
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
     * Get all of the files from the given directory (recursive).
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    private function includedFiles(): array
    {
        $pathFinder = $this->excluded();
        $nameFinder = clone $pathFinder;

        /**
         * Include directories
         * @see http://symfony.com/doc/current/components/finder.html#location
         */
        $includedPaths = $this->config->getIncludedPaths();
        foreach ($includedPaths as $path) {
            $nameFinder->path($path);
        }

//        /**
//         * Include directories
//         * @see http://symfony.com/doc/current/components/finder.html#location
//         */
//        $includedPaths = $this->config->getIncludedPaths();
//        $nameFinder->files()->filter(
//            function (SplFileInfo $file) use ($includedPaths) {
//                return in_array($file->getRelativePath(), $includedPaths);
//            }
//        );

        /**
         * Include Files
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        $includedFiles = $this->config->getIncludedFiles();
        foreach ($includedFiles as $file) {
            $nameFinder->path($file);
        }

        if(empty($includedPaths) && empty($includedFiles)) {
            $nameFinder->notPath('');
        }

        /**
         * Include Extensions
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        foreach ($this->config->getIncludedExtensions() as $pattern) {
            $nameFinder->name($pattern);
        }

        /**
         * Include Patterns - globs, strings, or regexes
         * @see http://symfony.com/doc/current/components/finder.html#file-name
         */
        foreach ($this->config->getIncludedPatterns() as $pattern) {
            $nameFinder->name($pattern);
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
}