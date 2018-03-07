<p align="center"><img src="https://www.dropbox.com/s/r3i5zt72ugtneou/asset-cdn.png?raw=1"></p>

<p align="center">
<a href="https://packagist.org/packages/arubacao/asset-cdn"><img src="https://img.shields.io/packagist/v/arubacao/asset-cdn.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://travis-ci.org/arubacao/asset-cdn"><img src="https://img.shields.io/travis/arubacao/asset-cdn/master.svg?style=flat-square" alt="Build Status"></a>
<a href="https://codecov.io/gh/arubacao/asset-cdn"><img src="https://img.shields.io/codecov/c/github/arubacao/asset-cdn.svg?style=flat-square" alt="Codecov"></a>
<a href="https://scrutinizer-ci.com/g/arubacao/asset-cdn"><img src="https://img.shields.io/scrutinizer/g/arubacao/asset-cdn.svg?style=flat-square" alt="Quality Score"></a>
<a href="https://packagist.org/packages/arubacao/asset-cdn"><img src="https://img.shields.io/packagist/dt/arubacao/asset-cdn.svg?style=flat-square" alt="Total Downloads"></a>
</p>


**Serve Laravel Assets from a Content Delivery Network (CDN)**

## Introduction

This package lets you **push**, **sync**, **delete** and **serve** assets to/from a CDN of your choice e.g. AWS Cloudfront.  
It adds helper methods **`mix_cdn()`** and **`asset_cdn()`**.

#### Simple Illustration
```bash
>>> env('USE_CDN')
=> true
```
```bash
$ php artisan asset-cdn:sync
```
```php
// head.blade.php
<link rel="stylesheet" href="{{ mix_cdn('/css/app.css') }}">
```
```html
<!-- Result -->
<link rel="stylesheet" href="https://cdn.mysite.com/css/app.css?id=081861342e950012abe3">
```

## Installation
Install this package via composer:

```bash
$ composer require arubacao/asset-cdn
```

Also register the service provider:  
_Only required for Laravel `<=5.4`, for Laravel `>=5.5` [auto-discovery](composer.json#L45) is enabled._
```PHP
// config/app.php

'providers' => [
    // Other Service Providers
    \Arubacao\AssetCdn\AssetCdnServiceProvider::class,
],
```
Notes:  

 - `arubacao/asset-cdn` is functional and fully tested for Laravel `5.4`, `5.5`, `5.6` on PHP `7.0`, `7.1`, `7.2`

## Configuration

#### 1. Configure Filesystem 

_Only required if you plan to manage your assets via the provided commands: `asset-cdn:push`, `asset-cdn:sync`, `asset-cdn:empty`_


`arubacao/asset-cdn` utilizes [Laravel's Filesystem](https://laravel.com/docs/5.6/filesystem) to **push**, **sync**, **delete** assets to/from the CDN of your choice.
Therefore, you have to configure and define a filesystem specific for CDN purposes. 
Please follow the [official documentation]((https://laravel.com/docs/5.6/filesystem)).

If you plan to use AWS S3/Cloudfront you can use this configuration:
```php
// config/filesystem.php

'asset-cdn' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'bucket' => env('AWS_CDN_BUCKET'),
],
```

#### 2. Publish Config File
```bash
$ php artisan vendor:publish --provider="Arubacao\AssetCdn\AssetCdnServiceProvider"
```

#### 3. Edit `cdn_url` and `filesystem.disk`
```php
// config/asset-cdn.php

[
    'cdn_url' => 'https://cdn.mysite.com',
    'filesystem' => [
        'disk' => 'asset-cdn',
    ],
]
```

#### 4. Edit `files` in `config/asset-cdn.php`
_Only required if you plan to manage your assets via the provided commands: `asset-cdn:push`, `asset-cdn:sync`, `asset-cdn:empty`_

**`files` always assumes a relative path from the `public` directoy**

- `ignoreDotFiles`  
Excludes "hidden" directories and files (starting with a dot).

- `ignoreVCS`  
Ignore version control directories.

- `include`  
**Any** file that matches at least one `include` rule, will be included. **No** file is included by default.

    - `paths`  
    Define paths that should be available on the CDN.  
    The following example will match **any** file in **any** `js` or `css` path it can find in the `public` directory.

```php
'include' => [
    'paths' => [
        'js', 
        'css'
    ],
]

/*
 * This config would try to find:
 * '/var/www/html/public/js'
 * '/var/www/html/public/css'
 * but also any other 'js' or 'css' path e.g.
 * '/var/www/html/public/vendor/js'
 * '/var/www/html/public/vendor/css'
 * You could explicitly exclude paths later
 */
```

    - `files`
    Define files that should be available on the CDN.  
    The following example will match **any** file that starts with `js/back.app.js` in the `public` directory.

```php
'include' => [
    'files' => [
        'js/app.js',
    ],
],

/*
 * This config would try to find:
 * '/var/www/html/public/js/app.js'
 * but also any other file that matches the path + filename e.g.
 * '/var/www/html/public/vendor/js/app.js'
 * You could explicitly exclude these files later
 */
```

    - `extensions`
    Define filetypes that should be available on the CDN.  
    The following example will match **any** file of type `*.css` or `*.js` in the `public` directory.
    
```php
'include' => [
    'extensions' => [
        '.js',
        '.css',
    ],
],
```

    - `patterns`
    Define patterns for files that should be available on the CDN.  
    The following example will match **any** file that starts with letters `a` or `b` in the `public` directory.

```php
/*
 * Patterns can be globs, strings, or regexes
 */
 
'include' => [
    'patterns' => [
        '/^[a-b]/i', //  starting with letters a-b
    ],
],
```

- `exclude`  
**Any** file that matches at least one `exclude` rule, will be excluded. Files that are excluded will **never** be included, even if they have been explicitly included.
Rules are identical as described above.


#### 5. Set Additional Configurations for Uploaded Files

`filesystem.options` are passed directly to the [Filesystem](https://github.com/thephpleague/flysystem/blob/1.0.43/src/FilesystemInterface.php#L232) 
which eventually calls the underlying Storage driver e.g. S3.  
Please refer to the corresponding storage driver documentation for available configuration options.  
The following example is recommended for AWS S3.  

```php
// config/asset-cdn.php

[
    'filesystem' => [
        'disk' => 'asset-cdn',
        'options' => [
            'ACL' => 'public-read', // File is available to the public, independent of the S3 Bucket policy 
            'CacheControl' => 'max-age=31536000, public', // Sets HTTP Header 'cache-control'. The client should cache the file for max 1 year 
        ],
    ],
]
```

#### 6. Set Environment Variable `USE_CDN`
```dotenv
# .env

USE_CDN=true # Enables asset-cdn
USE_CDN=false # Disables asset-cdn. (default)
```

## Usage

#### Commands

**Recommended**  
Sync assets that have been defined in the config to the CDN. Only pushes changes/new assets. Deletes locally removed files on CDN.
```bash
$ php artisan asset-cdn:sync
```

Pushes assets that have been defined in the config to the CDN. Pushes all assets. Does not delete files on CDN.
```bash
$ php artisan asset-cdn:sync
```

Deletes all assets from CDN, independent from config file.
```bash
$ php artisan asset-cdn:empty
```

#### Serving Assets
Replace [`mix()`](https://laravel.com/docs/5.6/helpers#method-mix) with `mix_cdn()`.   
Replace [`asset()`](https://laravel.com/docs/5.6/helpers#method-asset) with `asset_cdn()`.   


## Todo's:

 - Write README.MD
 - Video Tutorial: How to use S3/Cloudfront 
 - Write test for `ignoreVCS` finder config
 - Write test for `ignoreDotFiles` finder config
 - Extend `CombinedFinderTest`
