<?php

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

if (! function_exists('mix_cdn')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Exception
     */
    function mix_cdn($path, $manifestDirectory = '')
    {
        if (! config('asset-cdn.use_cdn')) {
            return mix($path, $manifestDirectory);
        }

        static $manifests = [];

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            throw new Exception("Unable to locate Mix file: {$path}.");
        }

        $cdnUrl = config('asset-cdn.cdn_url');
        // Remove slashes from ending of the path
        $cdnUrl = rtrim($cdnUrl, '/');

        return new HtmlString($cdnUrl.$manifestDirectory.$manifest[$path]);
    }
}

if (! function_exists('asset_cdn')) {

    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @return string
     */
    function asset_cdn($path)
    {
        if (! config('asset-cdn.use_cdn')) {
            return asset($path);
        }

        $cdnUrl = config('asset-cdn.cdn_url');
        // Remove slashes from ending of the path
        $cdnUrl = rtrim($cdnUrl, '/');

        return $cdnUrl.'/'.trim($path, '/');
    }
}
