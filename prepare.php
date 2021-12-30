#!/usr/bin/env php
<?php
require __DIR__ . '/build/Preprocessor.php';

$p = new Preprocessor(__DIR__);

$p->addLibrary((new Library('openssl'))
    ->withUrl('https://www.openssl.org/source/openssl-1.1.1m.tar.gz')
    ->withConfigure('./config -static --static no-shared --prefix=/usr/openssl')
    ->withLdflags('-L/usr/openssl/lib')
);

$p->addLibrary(
    (new Library('curl'))
        ->withUrl('https://curl.se/download/curl-7.80.0.tar.gz')
        ->withConfigure("autoreconf -fi && \ \n" .
            "./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl")
        ->withLdflags('-L/usr/curl/lib')
);

$p->addLibrary(
    (new Library('libiconv'))
        ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
        ->withConfigure('./configure --prefix=/usr enable_static=yes enable_shared=no')
);

$p->addLibrary(
    (new Library('sqlite3'))
        ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
        ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
);

$p->addLibrary(
    (new Library('zlib'))
        ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
        ->withConfigure('./configure --prefix=/usr --static')
);

$p->addLibrary(
    (new Library('bzip2'))
        ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
        ->withMakeOptions('PREFIX=/usr/bzip2')
        ->withLdflags('-L/usr/bzip2/lib')
);

$p->addLibrary(
    (new Library('icu'))
        ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
        ->withConfigure('source/runConfigureICU Linux --enable-static --disable-shared')
);

$p->addLibrary(
    (new Library('oniguruma'))
        ->withUrl('https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7')
        ->withConfigure('./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared')
        ->withFile('oniguruma-6.9.7.tar.gz')
);

$p->addLibrary(
    (new Library('zip'))
        ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
        ->withConfigure('cmake . -DBUILD_SHARED_LIBS=OFF -DCMAKE_INSTALL_PREFIX=/usr')
);

$p->addLibrary(
    (new Library('c-ares'))
        ->withUrl('https://c-ares.org/download/c-ares-1.18.1.tar.gz')
        ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
);

$p->addExtension(
    (new Extension('openssl'))
        ->withOptions('--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl')
);
$p->addExtension((new Extension('curl'))->withOptions('--with-curl=/usr/curl'));
$p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=/usr'));
$p->addExtension((new Extension('bz2'))->withOptions('--with-bz2'));
$p->addExtension((new Extension('bcmath'))->withOptions('--enable-bcmath'));
$p->addExtension((new Extension('pcntl'))->withOptions('--enable-pcntl'));
$p->addExtension((new Extension('filter'))->withOptions('--enable-filter'));
$p->addExtension((new Extension('session'))->withOptions('--enable-session'));
$p->addExtension((new Extension('tokenizer'))->withOptions('--enable-tokenizer'));
$p->addExtension((new Extension('mbstring'))->withOptions('--enable-mbstring'));
$p->addExtension((new Extension('ctype'))->withOptions('--enable-ctype'));
$p->addExtension((new Extension('zlib'))->withOptions('--with-zlib'));
$p->addExtension((new Extension('zip'))->withOptions('--with-zip'));
$p->addExtension(
    (new Extension('swoole'))
        ->withOptions('--enable-swoole --enable-sockets --enable-mysqlnd --enable-http2 --enable-swoole-json --enable-swoole-curl --enable-cares')
);
$p->addExtension((new Extension('posix'))->withOptions('--enable-posix'));
$p->addExtension((new Extension('sockets'))->withOptions('--enable-sockets'));
$p->addExtension((new Extension('pdo'))->withOptions('--enable-pdo'));
$p->addExtension((new Extension('phar'))->withOptions('--enable-phar'));
$p->addExtension((new Extension('mysqlnd'))->withOptions('--enable-mysqlnd'));
$p->addExtension((new Extension('mysqlnd'))->withOptions('--enable-mysqlnd'));
$p->addExtension((new Extension('intl'))->withOptions('--enable-intl'));
$p->addExtension((new Extension('fileinfo'))->withOptions('--enable-fileinfo'));
$p->addExtension((new Extension('pdo_mysql'))->withOptions('--with-pdo_mysql'));
$p->addExtension((new Extension('pdo-sqlite'))->withOptions('--with-pdo-sqlite'));
$p->addExtension((new Extension('sqlite3'))->withOptions('--with-sqlite3'));

$p->gen();
$p->stats();
