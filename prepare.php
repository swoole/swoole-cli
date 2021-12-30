#!/usr/bin/env php
<?php
require __DIR__ . '/build/Preprocessor.php';

$p = new Preprocessor(__DIR__);

function install_openssl(Preprocessor $p)
{
    $p->addLibrary((new Library('openssl'))
        ->withUrl('https://www.openssl.org/source/openssl-1.1.1m.tar.gz')
        ->withConfigure('./config -static --static no-shared --prefix=/usr/openssl')
        ->withLdflags('-L/usr/openssl/lib')
        ->withPkgConfig('/usr/openssl/lib/pkgconfig')
    );
}

function install_curl(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.80.0.tar.gz')
            ->withConfigure("autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl")
            ->withLdflags('-L/usr/curl/lib')
            ->withPkgConfig('/usr/curl/lib/pkgconfig')
    );
}

function install_libiconv(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withConfigure('./configure --prefix=/usr enable_static=yes enable_shared=no')
    );
}

function install_libxml2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxml2'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libxml2 --enable-static=yes --enable-shared=no')
            ->withLdflags('-L/usr/libxml2/lib')
            ->withPkgConfig('/usr/libxml2/lib/pkgconfig')
    );
}

function install_libxslt(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxslt'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libxslt --enable-static=yes --enable-shared=no')
            ->withLdflags('-L/usr/libxslt/lib')
            ->withPkgConfig('/usr/libxslt/lib/pkgconfig')
    );
}

function install_imagemagick(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-19.tar.gz')
            ->withConfigure('./configure --prefix=/usr/imagemagick --enable-static --disable-shared')
            ->withLdflags('-L/usr/imagemagick/lib')
            ->withPkgConfig('/usr/imagemagick/lib/pkgconfig')
    );
}

function install_libmemcached(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libmemcached'))
            ->withUrl('https://launchpad.net/libmemcached/1.0/1.0.18/+download/libmemcached-1.0.18.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libmemcached --enable-static --disable-shared')
            ->withLdflags('-L/usr/libmemcached/lib')
            ->withPkgConfig('/usr/libmemcached/lib/pkgconfig')
    );
}

function install_gmp(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('gmp'))
            ->withUrl('https://gmplib.org/download/gmp/gmp-6.2.1.tar.lz')
            ->withConfigure('./configure --prefix=/usr/gmp --enable-static --disable-shared')
            ->withLdflags('-L/usr/gmp/lib')
            ->withPkgConfig('/usr/gmp/lib/pkgconfig')
    );
}

function install_giflib(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('giflib'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withConfigure('./configure --prefix=/usr/giflib --enable-static --disable-shared')
            ->withLdflags('-L/usr/giflib/lib')
            ->withPkgConfig('/usr/giflib/lib/pkgconfig')
    );
}

function install_libpng(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libpng --enable-static --disable-shared')
            ->withLdflags('-L/usr/libpng/lib')
            ->withPkgConfig('/usr/libpng/lib/pkgconfig')
    );
}

function install_libjpeg(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libjpeg'))
            ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
            ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/libjpeg .')
            ->withLdflags('-L/usr/libjpeg/lib64')
            ->withFile('libjpeg-turbo-2.1.2.tar.gz')
            ->withPkgConfig('/usr/libjpeg/lib64/pkgconfig')
    );
}

function install_freetype(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('freetype'))
            ->withUrl('https://mirror.yongbok.net/nongnu/freetype/freetype-2.10.4.tar.gz')
            ->withConfigure('./configure --prefix=/usr/freetype --enable-static --disable-shared')
            ->withLdflags('-L/usr/freetype/lib')
            ->withPkgConfig('/usr/freetype/lib/pkgconfig')
    );
}

install_openssl($p);
install_curl($p);
install_libiconv($p);
install_libxml2($p);
install_libxslt($p);
//install_imagemagick($p);
//install_libmemcached($p);
install_gmp($p);
install_giflib($p);
install_libpng($p);
install_libjpeg($p);
install_freetype($p);

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
$p->addExtension((new Extension('sqlite3'))->withOptions('--with-sqlite3'));
$p->addExtension((new Extension('phar'))->withOptions('--enable-phar'));
$p->addExtension((new Extension('mysqlnd'))->withOptions('--enable-mysqlnd'));
$p->addExtension((new Extension('mysqlnd'))->withOptions('--enable-mysqlnd'));
$p->addExtension((new Extension('intl'))->withOptions('--enable-intl'));
$p->addExtension((new Extension('fileinfo'))->withOptions('--enable-fileinfo'));
$p->addExtension((new Extension('pdo_mysql'))->withOptions('--with-pdo_mysql'));
$p->addExtension((new Extension('pdo-sqlite'))->withOptions('--with-pdo-sqlite'));

$p->addExtension(
    (new Extension('xsl'))
        ->withOptions('--with-xsl --with-libxml=/usr/libxml2')
);

$p->addExtension(
    (new Extension('gmp'))
        ->withOptions('--with-gmp=/usr/gmp')
);

$p->addExtension(
    (new Extension('exif'))
        ->withOptions('--enable-exif')
);

$p->addExtension(
    (new Extension('xml'))
        ->withOptions('--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom')
);

$p->addExtension(
    (new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg=/usr/libjpeg  --with-freetype=/usr/freetype')
);

$p->addExtension((new Extension('redis'))
    ->withOptions('--enable-redis')
    ->withPeclVersion('5.3.5')
);

//$p->addExtension((new Extension('memcached'))
//    ->withOptions('--enable-memcached')
//    ->withPeclVersion('3.1.5')
//);
//
//$p->addExtension((new Extension('imagick'))
//    ->withOptions('--with-imagick')
//    ->withPeclVersion('3.6.0')
//);

$p->gen();
$p->info();
