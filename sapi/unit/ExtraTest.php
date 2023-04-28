<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;

class ExtraTest extends TestCase
{
    public $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';

    public function stopTestCurlIDN2(): void
    {
        $url = "http://国家电网.网址";
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
                'Referer: https://www.baidu.com',
                'Content-Type: text/html'
            ]
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        $response = curl_exec($ch);
        $responseHeader = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        if ($response !== false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            # list($header, $body) = explode("\r\n\r\n", $response, 2);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
        }
        curl_close($ch);

        $this->assertGreaterThanOrEqual(200, $responseHeader['http_code'], 'curl no support IDNA');
    }

    public function testCurlHttp3Client(): void
    {
        //https://curl.se/docs/http3.html

        $url = 'https://quic.tech:8443/';
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
        # https://curl.se/libcurl/c/CURLOPT_HTTP_VERSION.html
        # https://github.com/php/php-src/blob/master/ext/curl/interface.c
        # PHP-8.12 curl 库 暂不支持指定 HTTP3 协议 （http3 基于UDP的quic)

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $responseHeader = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        # list($header, $body) = explode("\r\n\r\n", $response, 2);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);

        $this->assertEquals(0, $errno, $error);
        $this->assertGreaterThanOrEqual(
            3,
            $responseHeader['http_version'],
            'no support http3; ' . $errno . ':' . $error
        );
    }

    public function noTestCurlSFTP(): void
    {
        $url = "sftp://username:password@localhost/";
        $url = "sftp://root:root@localhost/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url . "/text.txt");

        $response = curl_exec($ch);
        $responseHeader = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        # list($header, $body) = explode("\r\n\r\n", $response, 2);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($ch);
    }

    public function noTestCurlUpload(): void
    {
        $url = "http://192.168.3.26:8030/upload-serve.php";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $file = new \CURLFile('text.txt');
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["upload_name" => 'text.txt']);

        # curl_setopt($s, CURLOPT_UPLOAD, true);
        # curl_setopt($ch, CURLOPT_PUT, true);
        # curl_setopt($ch, CURLOPT_INFILE, fopen("text.txt", "r"));

        $response = curl_exec($ch);

        $responseHeader = curl_getinfo($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        # list($header, $body) = explode("\r\n\r\n", $response, 2);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_exec($ch);
    }

}
