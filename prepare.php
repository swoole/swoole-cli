#!/usr/bin/env php
<?php
require __DIR__ . '/build/Preprocessor.php';

$p = new Preprocessor(__DIR__);

$p->addLibrary(
    'openssl',
    'https://www.openssl.org/source/openssl-1.1.1m.tar.gz',
    './config -static --static no-shared --prefix=/usr/openssl');

$p->addLibrary(
    'curl',
    'https://curl.se/download/curl-7.80.0.tar.gz',
    "autoreconf -fi && \ \n" .
    "./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl");

$p->addLibrary(
    'libiconv',
    'https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz',
    './configure --prefix=/usr enable_static=yes enable_shared=no');

$p->addLibrary(
    'sqlite3',
    'https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz',
    './configure --prefix=/usr --enable-static --disable-shared');

$p->addLibrary(
    'zlib',
    'https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz',
    './configure --prefix=/usr --static');

$p->addLibrary(
    'bzip2',
    'https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz',
    'echo make libbzip2.a');

$p->addLibrary(
    'icu',
    'https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz',
    'source/runConfigureICU Linux --enable-static --disable-shared');

$p->addLibrary(
    'oniguruma',
    'https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7',
    './autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared',
    'oniguruma-6.9.7.tar.gz');

$p->addLibrary(
    'zip',
    'https://libzip.org/download/libzip-1.8.0.tar.gz',
    './configure --prefix=/usr --enable-static --disable-shared'
);

$p->addExtension('openssl', '--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl');
$p->addExtension('curl', '--with-curl=/usr/curl');
$p->addExtension('iconv', '--with-iconv=/usr');

$p->gen();
