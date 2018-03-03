<?php

namespace Arubacao\AssetCdn\Test\Finder;

class PatternFinderTest extends TestCase
{
    /** @test */
    public function find_patterns()
    {
        $fileConfig = [
            'include' => [
                'patterns' => [
                    '/\.php$/',  // ending with .php
                    '/^[a-b]/i', //  starting with letters a-b
                ],
            ],
        ];

        $expectedFiles = [
            'android-chrome-192x192.png',
            'apple-touch-icon.png',
            'browserconfig.xml',
            'css/back.css',
            'images/auth-background.jpg',
            'images/svg/blender.svg',
            'index.php',
            'js/back.app.js',
            'vendor/horizon/css/app.css',
            'vendor/horizon/css/app.css.map',
            'vendor/horizon/js/app.js',
            'vendor/horizon/js/app.js.map',
        ];

        $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
    }

    /** @test */
    public function find_patterns_and_exclude_some()
    {
        $fileConfig = [
            'include' => [
                'patterns' => [
                    '/\.php$/',  // ending with .php
                    '/^[a-b]/i', //  starting with letters a-b
                ],
            ],
            'exclude' => [
                'patterns' => [
                    '/\.js$/',  // ending with .js
                ],
            ],
        ];

        $expectedFiles = [
            'android-chrome-192x192.png',
            'apple-touch-icon.png',
            'browserconfig.xml',
            'css/back.css',
            'images/auth-background.jpg',
            'images/svg/blender.svg',
            'index.php',
            'vendor/horizon/css/app.css',
            'vendor/horizon/css/app.css.map',
            'vendor/horizon/js/app.js.map',
        ];

        $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
    }
}
