<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use imagick;
use ImagickDraw;
use ImagickPixel;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine\Http2\Client;
use Swoole\Http2\Request;

use function Swoole\Coroutine\run;

final class MainTest extends TestCase
{
    public function testExtesnions(): void
    {
        $exts = get_loaded_extensions();
        $this->assertContains('swoole', $exts);
        $this->assertContains('gd', $exts);
        $this->assertContains('imagick', $exts);
        # $this->assertContains('opcache', $exts);
        $this->assertContains('redis', $exts);
        # $this->assertContains('mongodb', $exts);
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

    public function testCurlFeature(): void
    {
        $reflector = new \ReflectionExtension('curl');
        ob_start();
        $reflector->info();
        $output = strip_tags(ob_get_clean());
        preg_match('/^AsynchDNS (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: c-ares no found');
        # preg_match('/^IDN (?:=>)?(.*)$/m', $output, $matches);
        # $this->assertEquals('Yes', trim($matches[1]), 'library: libidn2 no found');
        preg_match('/^libz (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: zlib no found');
        preg_match('/^SSL (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: openssl no found');
        preg_match('/^HTTP2 (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: nghttp2 no found');
        preg_match('/^BROTLI (?:=>)?(.*)$/m', $output, $matches);
        $this->assertEquals('Yes', trim($matches[1]), 'library: brotli no found');
        preg_match('/^libSSH (?:Version =>)?(.*)$/m', $output, $matches);
        $this->assertTrue((strpos(trim($matches[1]), 'libssh2') !== false), 'library: libSSH no found');
        preg_match('/^ZLib (?:Version =>)?(.*)$/m', $output, $matches);
        $this->assertNotEmpty(trim($matches[1]), 'library: ZLib no found');

    }

    public $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';

    public function testCurlHTTP2Client(): void
    {
        $url = 'https://www.jingjingxyk.com/';
        $userAgent = $this->userAgent;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'User-Agent: ' . $userAgent,
                'Content-Type: text/html'
            ]
        );
        # $ca='/tmp/ssl/cacert.pem';
        # curl_setopt($ch, CURLOPT_CAINFO, $ca);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $responseHeader = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        $this->assertEquals(0, $errno, $error);
        $this->assertGreaterThanOrEqual(
            2,
            $responseHeader['protocol'],
            'no support http2; ' . $errno . ':' . $error
        );
    }


    public function testSwooleHttp2Client(): void
    {
        ini_set('default_socket_timeout', 60);
        run(function () {
            $domain = 'www.jingjingxyk.com';
            $cli = new Client($domain, 443, true);
            $cli->set([
                'timeout' => 15,
                'ssl_host_name' => $domain,
                //'ssl_verify_peer' => true,
                //'ssl_cafile' => '/tmp/ssl/cacert.pem',
            ]);
            $cli->connect();

            $userAgent = $this->userAgent;
            $req = new Request();
            $req->method = 'GET';
            $req->path = '/';
            $req->headers = [
                'host' => $domain,
                'user-agent' => $userAgent,
                'accept' => 'text/html,application/xhtml+xml,application/xml',
                'accept-encoding' => 'gzip'
            ];
            $cli->send($req);
            $response = $cli->recv();
            $this->assertEquals(200, $response->statusCode, "no support http2");
        });
    }
}
