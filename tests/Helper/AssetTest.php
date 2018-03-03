<?php

namespace Arubacao\AssetCdn\Test\Helper;

use Arubacao\AssetCdn\Test\TestCase;

class AssetTest extends TestCase
{
    /** @test */
    public function asset_cdn_falls_back_to_asset_if_disabled()
    {
        $this->app['config']->set('asset-cdn.use_cdn', false);
        $urls = [
            asset_cdn('images/auth-background.jpg'),
            asset_cdn('/images/auth-background.jpg'),
        ];
        foreach ($urls as $url) {
            $this->assertSame('http://localhost/images/auth-background.jpg', $url);
        }
    }

    /** @test */
    public function asset_cdn_returns_correct_url()
    {
        $urls = [
            asset_cdn('images/auth-background.jpg'),
            asset_cdn('/images/auth-background.jpg'),
        ];
        foreach ($urls as $url) {
            $this->assertSame('http://cdn.localhost/images/auth-background.jpg', $url);
        }
    }
}
