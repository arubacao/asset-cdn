<?php

namespace Arubacao\AssetCdn;

use Illuminate\Foundation\Application;

class Config
{
    const INCLUDE = 'include';
    const EXCLUDE = 'exclude';

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $publicPath;

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->config = $app->make('config')->get('asset-cdn.files');
        $this->publicPath = $app->make('path.public');
    }

    public function ignoreDotFiles(): bool
    {
        return $this->config['ignoreDotFiles'];
    }

    public function ignoreVCS(): bool
    {
        return $this->config['ignoreVCS'];
    }

    public function getIncludedPaths(): array
    {
        return $this->paths(self::INCLUDE);
    }

    public function getExcludedPaths(): array
    {
        return $this->paths(self::EXCLUDE);
    }

    public function getIncludedExtensions(): array
    {
        return $this->extensions(self::INCLUDE);
    }

    public function getExcludedExtensions(): array
    {
        return $this->extensions(self::EXCLUDE);
    }

    public function getIncludedPatterns(): array
    {
        return $this->config[self::INCLUDE]['patterns'];
    }

    public function getExcludedPatterns(): array
    {
        return $this->config[self::EXCLUDE]['patterns'];
    }

    public function getIncludedFiles(): array
    {
        return $this->files(self::INCLUDE);
    }

    public function getExcludedFiles(): array
    {
        return $this->files(self::EXCLUDE);
    }

    private function paths(string $type): array
    {
        return array_map(
            function ($path) {
                return $this->cleanPath($path).'/';
            },
            $this->config[$type]['paths']
        );
    }

    private function files(string $type): array
    {
        return array_map(
            function ($path) {
                return $this->cleanPath($path);
            },
            $this->config[$type]['files']
        );
    }

    private function extensions(string $type): array
    {
        return array_map(
            function ($extension) {
                return '*' . $this->start($extension, '.');
            },
            $this->config[$type]['extensions']
        );
    }

    /**
     * Remove any extra slashes '/' from the path.
     *
     * @param string $path
     * @return string
     */
    private function cleanPath(string $path): string
    {
        return rtrim(ltrim($path, '/'), '/');
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $prefix
     * @return string
     */
    private function start($value, $prefix)
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }

    /**
     * @return string
     */
    public function getPublicPath(): string
    {
        return $this->publicPath;
    }
}