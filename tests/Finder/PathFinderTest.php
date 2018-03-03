<?php

namespace Arubacao\AssetCdn\Test\Finder;

class PathFinderTest extends TestCase
{
    /** @test */
    public function find_all_js_paths()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'paths' => [
                        'js',
                    ],
                ],
            ],
            1 => [
                'include' => [
                    'paths' => [
                        '/js',
                    ],
                ],
            ],
            2 => [
                'include' => [
                    'paths' => [
                        'js/',
                    ],
                ],
            ],
        ];

        $expectedFiles = [
            'js/back.app.js',
            'js/front.app.js',
            'vendor/horizon/js/app.js',
            'vendor/horizon/js/app.js.map',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_all_js_paths_exclude_one()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'paths' => [
                        'js',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        'vendor/horizon/js',
                    ],
                ],
            ],
            1 => [
                'include' => [
                    'paths' => [
                        '/js',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        '/vendor/horizon/js',
                    ],
                ],
            ],
            2 => [
                'include' => [
                    'paths' => [
                        'js/',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        'vendor/horizon/js/',
                    ],
                ],
            ],
            3 => [
                'include' => [
                    'paths' => [
                        '/js/',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        '/vendor/horizon/js/',
                    ],
                ],
            ],
        ];

        $expectedFiles = [
            'js/back.app.js',
            'js/front.app.js',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_sub_path()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'paths' => [
                        'vendor/horizon/img',
                    ],
                ],
            ],
            1 => [
                'include' => [
                    'paths' => [
                        '/vendor/horizon/img',
                    ],
                ],
            ],
            2 => [
                'include' => [
                    'paths' => [
                        'vendor/horizon/img/',
                    ],
                ],
            ],
        ];

        $expectedFiles = [
            'vendor/horizon/img/horizon.svg',
            'vendor/horizon/img/sprite.svg',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_multiple_paths()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'paths' => [
                        'vendor/horizon/img',
                        'images/svg',
                    ],
                ],
            ],
            1 => [
                'include' => [
                    'paths' => [
                        '/vendor/horizon/img',
                        '/images/svg',
                    ],
                ],
            ],
            2 => [
                'include' => [
                    'paths' => [
                        'vendor/horizon/img/',
                        'images/svg/',
                    ],
                ],
            ],
        ];

        $expectedFiles = [
            'images/svg/blender.svg',
            'vendor/horizon/img/horizon.svg',
            'vendor/horizon/img/sprite.svg',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_multiple_paths_exclude_one()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'paths' => [
                        'vendor/horizon/img',
                        'images',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        'images/svg',
                    ],
                ],
            ],
            1 => [
                'include' => [
                    'paths' => [
                        '/vendor/horizon/img',
                        '/images',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        '/images/svg',
                    ],
                ],
            ],
            2 => [
                'include' => [
                    'paths' => [
                        'vendor/horizon/img/',
                        'images/',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        'images/svg/',
                    ],
                ],
            ],
            3 => [
                'include' => [
                    'paths' => [
                        '/vendor/horizon/img/',
                        '/images/',
                    ],
                ],
                'exclude' => [
                    'paths' => [
                        '/images/svg/',
                    ],
                ],
            ],
        ];

        $expectedFiles = [
            'images/auth-background.jpg',
            'images/og-image.png',
            'images/video.play.png',
            'vendor/horizon/img/horizon.svg',
            'vendor/horizon/img/sprite.svg',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_no_paths()
    {
        $fileConfig = [
            'include' => [
                'paths' => [],
            ],
        ];

        $expectedFiles = [];

        $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
    }
}
