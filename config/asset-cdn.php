<?php

return [

    'use_cdn' => env('USE_CDN', false),

    'filesystem' => 'asset-cdn',

    'files' => [

        // Excludes "hidden" directories and files (starting with a dot).
        'ignoreDotFiles' => true,

        // Forces the finder to ignore version control directories.
        'ignoreVCS' => true,

        'include' => [
            'paths' => [
                //
            ],
            'files' => [
                //
            ],
            'extensions' => [
                //
            ],
            'patterns' => [
                //
            ],
        ],

        'exclude' => [
            'paths' => [
                //
            ],
            'files' => [
                //
            ],
            'extensions' => [
                //
            ],
            'patterns' => [
                //
            ],
        ],
    ],

];
