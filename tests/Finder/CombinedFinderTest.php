<?php

namespace Arubacao\AssetCdn\Test\Finder;

class CombinedFinderTest extends TestCase
{
    /** @test */
    public function find_all_js_paths_and_backcss_and_robotstxt()
    {
        $fileConfig = [
            'include' => [
                'paths' => [
                    'js',
                ],
                'files' => [
                    'css/back.css',
                    'robots.txt',
                ]
            ]
        ];

        $expectedFiles = [
            'css/back.css',
            'js/back.app.js',
            'js/front.app.js',
            'robots.txt',
            'vendor/horizon/js/app.js',
            'vendor/horizon/js/app.js.map',
        ];

        $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
    }

    /** @test */
    public function find_all_js_paths_and_backcss_and_robotstxt_exclude_vendor_path_and_backjs()
    {
        $fileConfig = [
            'include' => [
                'paths' => [
                    'js',
                ],
                'files' => [
                    'css/back.css',
                    'robots.txt',
                ]
            ],
            'exclude' => [
                'paths' => [
                    'vendor',
                ],
                'files' => [
                    'js/back.app.js',
                ]
            ]
        ];

        $expectedFiles = [
            'css/back.css',
            'js/front.app.js',
            'robots.txt',
        ];

        $this->assertFilesMatchConfig($expectedFiles, $fileConfig);
    }

}