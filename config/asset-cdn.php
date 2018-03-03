<?php

return [

    'use_cdn' => env('USE_CDN', false),

    'cdn_url' => '',

    'filesystem' => [

        'disk' => 'asset-cdn',

        'options' => [
            'ACL' => 'public-read',
            'CacheControl' => 'max-age=31536000, public',
        ],
    ],

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
