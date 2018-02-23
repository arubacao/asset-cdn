<?php

namespace Arubacao\AssetCdn\Test\Finder;

class FileFinderTest extends TestCase
{
    /** @test */
    public function find_three_files()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'files' => [
                        'js/back.app.js',
                        'img/layout/ph3x2.png',
                        'mstile-150x150.png',
                    ]
                ]
            ],
            1 => [
                'include' => [
                    'files' => [
                        '/js/back.app.js',
                        '/img/layout/ph3x2.png',
                        '/mstile-150x150.png',
                    ]
                ]
            ]
        ];

        $expectedFiles = [
            'img/layout/ph3x2.png',
            'js/back.app.js',
            'mstile-150x150.png',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }

    /** @test */
    public function find_all_fontawesomeotf()
    {
        $fileConfigs = [
            0 => [
                'include' => [
                    'files' => [
                        'FontAwesome.otf',
                    ]
                ]
            ],
            1 => [
                'include' => [
                    'files' => [
                        '/FontAwesome.otf',
                    ]
                ]
            ]
        ];

        $expectedFiles = [
            'fonts/fontawesome/FontAwesome.otf',
            'fonts/FontAwesome.otf',
            'fonts/vendor/fontawesome/FontAwesome.otf',
        ];

        foreach ($fileConfigs as $fileConfig) {
            $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
        }
    }
}