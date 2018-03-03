<?php

namespace Arubacao\AssetCdn\Test\Helper;

use Illuminate\Support\Facades\Log;
use Arubacao\AssetCdn\Test\TestCase;

class MixTest extends TestCase
{
    /** @test */
    public function mix_cdn_falls_back_to_mix_if_disabled()
    {
        $this->app['config']->set('asset-cdn.use_cdn', false);
        $urls = [
            mix_cdn('js/back.app.js'),
            mix_cdn('/js/back.app.js'),
        ];
        foreach ($urls as $url) {
            $this->assertInstanceOf('Illuminate\Support\HtmlString', $url);
            $this->assertSame('/js/back.app.js?id=0dd41baa9f9a73f1dd97', $url->toHtml());
        }
    }

    /** @test */
    public function mix_cdn_returns_correct_url()
    {
        $urls = [
            mix_cdn('js/back.app.js'),
            mix_cdn('/js/back.app.js'),
        ];
        foreach ($urls as $url) {
            $this->assertInstanceOf('Illuminate\Support\HtmlString', $url);
            $this->assertSame('http://cdn.localhost/js/back.app.js?id=0dd41baa9f9a73f1dd97', $url->toHtml());
        }
    }

    /** @test */
    public function mix_cdn_reports_exception_with_unknown_file()
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(
                function ($message) {
                    $this->assertSame('Unable to locate Mix file: /js/unknown.app.js.', $message);

                    return true;
                }
            )
            ->andReturnNull();
        mix_cdn('js/unknown.app.js');
    }

    /** @test */
    public function mix_cdn_throws_exception_with_no_manifest_file()
    {
        $this->app->bind('path.public', function () {
            return __DIR__.'/../testfiles/dummy';
        });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The Mix manifest does not exist.');

        mix_cdn('js/unknown.app.js');
    }
}
