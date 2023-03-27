<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;

class ExtraTest extends TestCase
{
    public function testCurlIDN2(): void
    {
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
        $responseHheader = curl_getinfo($ch1);
        curl_close($ch1);

        $this->assertGreaterThanOrEqual(200, $responseHheader['http_code'], 'curl no support IDNA');

        $url2 = 'https://www.swoole.com/';
        $userAgent = 'swoole-cli-test';
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
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch2);
        $responseHheader = curl_getinfo($ch2);
        curl_close($ch2);
        $this->assertGreaterThanOrEqual(2, $responseHheader['protocol'], 'no support http2');
    }
}
