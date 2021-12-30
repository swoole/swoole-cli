#!/usr/bin/env php
<?php

class Preprocessor
{
    protected array $libraryList = [];
    protected string $libraryDir = __DIR__ . '/pool/lib';
    protected int $maxJob = 8;

    function addLibrary(string $name, string $url, string $configure = '')
    {
        $file = basename($url);
        if (!is_file($this->libraryDir . '/' . $file)) {
            echo `wget $url -P {$this->libraryDir}`;
            echo $file;
        }

        $this->libraryList[] = [
            'name' => $name,
            'file' => $file,
            'configure' => $configure,
        ];
    }

    function gen()
    {
        ob_start();
        include __DIR__ . '/build/dockerfile.php';
        file_put_contents(__DIR__.'/Dockerfile', ob_get_clean());
    }

    /**
     * make -j {$n}
     * @param int $n
     */
    function setMaxJob(int $n)
    {
        $this->maxJob = $n;
    }
}

$p = new Preprocessor;

$p->addLibrary(
    'openssl',
    'https://www.openssl.org/source/openssl-1.1.1m.tar.gz',
    './config -static --static no-shared');

$p->addLibrary(
    'curl',
    'https://curl.se/download/curl-7.80.0.tar.gz',
    "autoreconf -fi && \ \n./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl");

$p->addLibrary(
    'libiconv',
    'https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz',
    './configure --prefix=/usr enable_static=yes enable_shared=no');

$p->addLibrary(
    'sqlite3',
    'https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz',
    './configure --enable-static --disable-shared');

$p->gen();
