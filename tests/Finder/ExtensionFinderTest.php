<?php

namespace Arubacao\AssetCdn\Test\Finder;

class ExtensionFinderTest extends TestCase
{
    /** @test */
    public function find_all_js_and_php_extensions()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'extensions' => [
                        '.js',
                        '.php',
                    ]
                ]
            ],
            1 => [
                'include' => [
                    'files' => [
                        'js',
                        'php',
                    ]
                ]
            ]
        ];

        $expectedFiles = [
            'index.php',
            'js/back.app.js',
            'js/front.app.js',
            'vendor/horizon/js/app.js',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_all_js_and_php_extensions_but_exclude_php_again()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'extensions' => [
                        '.js',
                        '.php',
                    ]
                ],
                'exclude' => [
                    'extensions' => [
                        '.php',
                    ]
                ]
            ],
            1 => [
                'include' => [
                    'extensions' => [
                        'js',
                        'php',
                    ]
                ],
                'exclude' => [
                    'extensions' => [
                        'php',
                    ]
                ]
            ]
        ];

        $expectedFiles = [
            'js/back.app.js',
            'js/front.app.js',
            'vendor/horizon/js/app.js',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }
}