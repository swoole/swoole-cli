<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use imagick;
use ImagickPixel;
use ImagickDraw;

use function Swoole\Coroutine\run;
use function Swoole\Coroutine\go;

final class MainTest extends TestCase
{
    public function testExtesnions(): void
    {
        $exts = get_loaded_extensions();
        $this->assertContains('swoole', $exts);
        $this->assertContains('gd', $exts);
        $this->assertContains('imagick', $exts);
        $this->assertContains('opcache', $exts);
        $this->assertContains('redis', $exts);
        $this->assertContains('mongodb', $exts);
    }

    public function testGd(): void
    {
        $info = gd_info();
        $this->assertEquals($info["FreeType Support"], true);
        $this->assertEquals($info["GIF Read Support"], true);
        $this->assertEquals($info["GIF Create Support"], true);
        $this->assertEquals($info["JPEG Support"], true);
        $this->assertEquals($info["PNG Support"], true);
        $this->assertEquals($info["WBMP Support"], true);

        $this->assertEquals($info["XPM Support"], false);
        $this->assertEquals($info["XBM Support"], true);
        $this->assertEquals($info["WebP Support"], true);

        $this->assertEquals($info["BMP Support"], true);
        $this->assertEquals($info["TGA Read Support"], true);
        $this->assertEquals($info["JIS-mapped Japanese Font Support"], false);
    }

    public function testImagick(): void
    {
        $img = new Imagick();
        $bg = new ImagickPixel();
        $bg->setColor('white');
        $ImagickDraw = new ImagickDraw();
        $ImagickDraw->setFont(__DIR__ . '/font.ttf');
        $ImagickDraw->setFontSize(20);

        $alphanum = 'ABXZRMHTL23456789';
        $string = substr(str_shuffle($alphanum), 2, 6);
        $img->newImage(85, 30, $bg);
        $img->annotateImage($ImagickDraw, 4, 20, 0, $string);
        $img->swirlImage(20);

        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));

        $img->drawImage($ImagickDraw);
        $this->assertTrue($img->setImageFormat('png'));
        $this->assertTrue($img->setImageFormat('jpeg'));
        $this->assertTrue($img->setImageFormat('webp'));

        $this->assertTrue($img->setCompression(imagick::COMPRESSION_BZIP));
        $this->assertTrue($img->setCompression(imagick::COMPRESSION_ZIP));
    }

    public function testIntl(): void
    {
        $reflector = new \ReflectionExtension('intl');
        ob_start();
        $reflector->info();
        $output = strip_tags(ob_get_clean());
        preg_match('/^ICU version (?:=>)?(.*)$/m', $output, $matches);
        $icuVersion = trim($matches[1]);
        $this->assertNotEmpty($icuVersion);


        $this->assertIsArray(\ResourceBundle::getLocales(''));
        $r = \ResourceBundle::create('root', 'ICUDATA', false);
        $this->assertNotEmpty($r->get("Version"));
        $this->assertNotTrue(
            intl_is_failure($r->getErrorCode()),
            'error_code: ' . $r->getErrorCode() . ':' . $r->getErrorMessage()
        );
    }

    public function testCurl(): void
    {
        $reflector = new \ReflectionExtension('curl');
        ob_start();
        $reflector->info();
        $output = strip_tags(ob_get_clean());
        preg_match('/^AsynchDNS (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: c-ares no found');
        preg_match('/^IDN (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: libidn2 no found');
        preg_match('/^libz (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: zlib no found');
        preg_match('/^SSL (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: openssl no found');
        preg_match('/^HTTP2 (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: nghttp2 no found');
        preg_match('/^BROTLI (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: brotli no found');


        $url1 = "http://国家电网.网址";
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, $url1);
        curl_setopt($ch1, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch1, CURLOPT_FILETIME, true);
        curl_setopt(
            $ch1,
            CURLOPT_HTTPHEADER,
            [
                'User-Agent: ' . $userAgent,
                'Referer: https://www.baidu.com',
                'Content-Type: text/html'
            ]
        );
        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, false);
        curl_exec($ch1);
        $responseheader = curl_getinfo($ch1);
        $errno = curl_errno($ch1);
        $error = curl_error($ch1);
        curl_close($ch1);
        $this->assertEquals(0,$errno,$error);
        $this->assertGreaterThanOrEqual(200, $responseheader['http_code'], 'curl no support IDNA');

        echo PHP_EOL;
        echo "==================";
        echo PHP_EOL;
        echo 'curl.cainfo=' . ini_get('curl.cainfo');
        echo PHP_EOL;
        echo 'openssl.cafile=' . ini_get('openssl.cafile');
        echo PHP_EOL;
        echo "==================";
        echo PHP_EOL;

        #  mkdir -p /tmp/ssl/
        #  wget  -O /tmp/ssl/cacert.pem https://curl.se/ca/cacert.pem


        $url2 = 'https://www.cloudflare.com/';
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $url2);
        curl_setopt($ch2, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch2, CURLOPT_FILETIME, true);
        curl_setopt(
            $ch2,
            CURLOPT_HTTPHEADER,
            [
                'User-Agent: ' . $userAgent,
                'Content-Type: text/html'
            ]
        );
        # $ca='/tmp/ssl/cacert.pem';
        # curl_setopt($ch2, CURLOPT_CAINFO, $ca);
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch2);
        $responseHeader = curl_getinfo($ch2);
        $errno = curl_errno($ch2);
        $error = curl_error($ch2);
        curl_close($ch2);

        $this->assertEquals(0,$errno,$error);
        $this->assertGreaterThanOrEqual(
            2,
            $responseHeader['protocol'],
            'no support http2; ' . $errno . ':' . $error
        );
    }

    public function testSwoole(): void
    {
        run(function () {
            $url= 'https://www.cloudflare.com/';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($ch);
            $responseHeader = curl_getinfo($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            $this->assertEquals(0,$errno,$error);
            $this->assertGreaterThanOrEqual(
                2,
                $responseHeader['protocol'],
                'no support http2 ; ' . $errno . ':' . $error
            );
        });
    }
}
