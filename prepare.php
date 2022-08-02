#!/usr/bin/env php
<?php
require __DIR__ . '/sapi/Preprocessor.php';

use SwooleCli\Preprocessor;
use SwooleCli\Library;
use SwooleCli\Extension;

if (empty($argv[1])) {
    $type = 'linux';
} else {
    $type = trim($argv[1]);
}

$p = new Preprocessor(__DIR__);
$p->setPhpSrcDir('/home/htf/soft/php-8.1.8');
$p->setDockerVersion('1.4');
$p->setSwooleDir('/home/htf/workspace/swoole');

$endCallback = function() {};

if ($type == 'macos') {
    define('WORKSPACE', '/Users/hantianfeng/workspace');
    $p->setWorkDir(WORKSPACE.'/cli-swoole');
    $p->setExtraLdflags('-L/usr/lib -framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup -lwebp -licudata -licui18n -licuio');
    $endCallback = function($p) {
        $makesh = file_get_contents(__DIR__.'/make.sh');
        $makesh = str_replace('/usr', WORKSPACE.'/opt/usr', $makesh);
        $makesh = str_replace('export PKG_CONFIG_PATH=', 'export PKG_CONFIG_PATH='.WORKSPACE.'/opt/usr/lib/pkgconfig:', $makesh);
        file_put_contents(__DIR__.'/make.sh', $makesh);
    };
}

// ================================================================================================
// Library
// ================================================================================================

function install_openssl(Preprocessor $p)
{
    $p->addLibrary((new Library('openssl'))
        ->withUrl('https://www.openssl.org/source/openssl-1.1.1m.tar.gz')
        ->withConfigure('./config -static --static no-shared --prefix=/usr/openssl')
        ->withLdflags('-L/usr/openssl/lib')
        ->withPkgConfig('/usr/openssl/lib/pkgconfig')
        ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
        ->withHomePage('https://www.openssl.org/')
    );
}

function install_libiconv(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withLdflags('-L/usr/libiconv/lib')
            ->withConfigure('./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
    );
}

// MUST be in the /usr directory
function install_libxml2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxml2'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no')
            ->withPkgName('libxml-2.0')
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
    );
}

// MUST be in the /usr directory
function install_libxslt(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxslt'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no')
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
    );
}

function install_imagemagick(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-19.tar.gz')
            ->withConfigure('./configure --prefix=/usr/imagemagick --with-zip=no --enable-static --disable-shared')
            ->withLdflags('-L/usr/imagemagick/lib')
            ->withPkgConfig('/usr/imagemagick/lib/pkgconfig')
            ->withPkgName('ImageMagick')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
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
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
    );
}

function install_giflib(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('giflib'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withMakeOptions('libgif.a')
            ->withLicense('http://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
    );
}

function install_libpng(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withLdflags('-L/usr/libpng/lib')
            ->withPkgConfig('/usr/libpng/lib/pkgconfig')
            ->withConfigure('./configure --prefix=/usr/libpng --enable-static --disable-shared')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
    );
}

function install_libjpeg(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libjpeg'))
            ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
            ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr .')
            ->withLdflags('-L/usr/lib64')
            ->withPkgConfig('/usr/lib64/pkgconfig')
            ->withFile('libjpeg-turbo-2.1.2.tar.gz')
            ->withHomePage('https://libjpeg-turbo.org/')
            ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
    );
}

function install_freetype(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('freetype'))
            ->withUrl('https://mirror.yongbok.net/nongnu/freetype/freetype-2.10.4.tar.gz')
            ->withConfigure('./configure --prefix=/usr/freetype --enable-static --disable-shared')
            ->withHomePage('https://freetype.org/')
            ->withLdflags('-L/usr/freetype/lib')
            ->withPkgName('freetyp2')
            ->withPkgConfig('/usr/freetype/lib/pkgconfig')
            ->withLicense('https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT', Library::LICENSE_SPEC)
    );
}

function install_libwebp(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared')
            ->withLdflags('-L/usr/libwebp/lib')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withPkgConfig('/usr/libwebp/lib/pkgconfig')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
    );
}

function install_sqlite3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('sqlite3'))
            ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
            ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
    );
}

function install_zlib(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('zlib'))
            ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
            ->withConfigure('./configure --prefix=/usr --static')
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
    );
}

function install_bzip2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('bzip2'))
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withMakeOptions('PREFIX=/usr/bzip2')
            ->withLdflags('-L/usr/bzip2/lib')
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
    );
}

function install_icu(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('icu'))
            ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
            ->withConfigure('source/runConfigureICU Linux --prefix=/usr --enable-static --disable-shared')
            ->withPkgName('icu-i18n')
            ->withHomePage('https://icu.unicode.org/')
            ->withLicense('https://github.com/unicode-org/icu/blob/main/icu4c/LICENSE', Library::LICENSE_SPEC)
    );
}

function install_oniguruma(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withUrl('https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared')
            ->withFile('oniguruma-6.9.7.tar.gz')
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
    );
}

// MUST be in the /usr directory
function install_zip(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('zip'))
            ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withConfigure('cmake . -DBUILD_SHARED_LIBS=OFF -DOPENSSL_USE_STATIC_LIBS=TRUE -DCMAKE_INSTALL_PREFIX=/usr')
            ->withPkgName('libzip')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
    );
}

function install_cares(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.18.1.tar.gz')
            ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
            ->withPkgName('libcares')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
    );
}

function install_libedit(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libedit'))
            ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libedit --enable-static --disable-shared')
            ->withPkgConfig('/usr/libedit/lib/pkgconfig')
            ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
            ->withHomePage('https://thrysoee.dk/editline/')
    );
}

function install_ncurses(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('ncurses'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
            ->withLicense('https://github.com/projectceladon/libncurses/blob/master/README', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/projectceladon/libncurses')
    );
}

function install_libsodium(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libsodium'))
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.18.tar.gz')
            ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
    );
}

function install_libyaml(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libyaml'))
            ->withUrl('http://pyyaml.org/download/libyaml/yaml-0.2.5.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libyaml --enable-static --disable-shared')
            ->withLdflags('-L/usr/libyaml/lib')
            ->withPkgConfig('/usr/libyaml/lib/pkgconfig')
            ->withPkgName('yaml-0.1')
            ->withLicense('https://pyyaml.org/wiki/LibYAML', Library::LICENSE_MIT)
            ->withHomePage('https://pyyaml.org/wiki/LibYAML')
    );
}

function install_brotli(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('brotli'))
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withConfigure("autoreconf -fi && ./configure --prefix=/usr --enable-static --disable-shared")
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/google/brotli')
    );
}

function install_curl(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.80.0.tar.gz')
            ->withConfigure("autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl ".
                "--without-librtmp --without-brotli --without-libidn2 --disable-ldap --disable-rtsp --without-zstd --without-nghttp2 --without-nghttp3"
            )
            ->withLdflags('-L/usr/curl/lib')
            ->withPkgConfig('/usr/curl/lib/pkgconfig')
            ->withPkgName('libcurl')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
    );
}

install_openssl($p);
install_libiconv($p);
install_libxml2($p);
install_libxslt($p);
install_gmp($p);
install_giflib($p);
install_libpng($p);
install_libjpeg($p);
install_freetype($p);
install_libwebp($p);
install_sqlite3($p);
install_zlib($p);
install_bzip2($p);
install_icu($p);
install_oniguruma($p);
install_zip($p);
//install_brotli($p);
install_cares($p);
//install_ncurses($p);
//install_libedit($p);
install_imagemagick($p);
install_curl($p);
install_libsodium($p);
install_libyaml($p);

// ================================================================================================
// PHP Extension
// ================================================================================================

$p->addExtension(
    (new Extension('openssl'))
        ->withOptions('--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl')
);

$p->addExtension((new Extension('curl'))->withOptions('--with-curl'));
$p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=/usr/libiconv'));
$p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=/usr/bzip2'));
$p->addExtension((new Extension('bcmath'))->withOptions('--enable-bcmath'));
$p->addExtension((new Extension('pcntl'))->withOptions('--enable-pcntl'));
$p->addExtension((new Extension('filter'))->withOptions('--enable-filter'));
$p->addExtension((new Extension('session'))->withOptions('--enable-session'));
$p->addExtension((new Extension('tokenizer'))->withOptions('--enable-tokenizer'));
$p->addExtension((new Extension('mbstring'))->withOptions('--enable-mbstring'));
$p->addExtension((new Extension('ctype'))->withOptions('--enable-ctype'));
$p->addExtension((new Extension('zlib'))->withOptions('--with-zlib'));
$p->addExtension((new Extension('zip'))->withOptions('--with-zip'));
$p->addExtension((new Extension('posix'))->withOptions('--enable-posix'));
$p->addExtension((new Extension('sockets'))->withOptions('--enable-sockets'));
$p->addExtension((new Extension('pdo'))->withOptions('--enable-pdo'));
$p->addExtension((new Extension('sqlite3'))->withOptions('--with-sqlite3'));
$p->addExtension((new Extension('phar'))->withOptions('--enable-phar'));
$p->addExtension((new Extension('mysqlnd'))->withOptions('--enable-mysqlnd'));
$p->addExtension((new Extension('mysqli'))->withOptions('--with-mysqli'));
$p->addExtension((new Extension('intl'))->withOptions('--enable-intl'));
$p->addExtension((new Extension('fileinfo'))->withOptions('--enable-fileinfo'));
$p->addExtension((new Extension('pdo_mysql'))->withOptions('--with-pdo_mysql'));
$p->addExtension((new Extension('pdo-sqlite'))->withOptions('--with-pdo-sqlite'));
$p->addExtension((new Extension('soap'))->withOptions('--enable-soap'));
$p->addExtension((new Extension('xsl'))->withOptions('--with-xsl'));
$p->addExtension((new Extension('gmp'))->withOptions('--with-gmp=/usr/gmp'));
$p->addExtension((new Extension('exif'))->withOptions('--enable-exif'));
$p->addExtension((new Extension('sodium'))->withOptions('--with-sodium'));
//$p->addExtension((new Extension('readline'))->withOptions('--with-libedit'));
//$p->addExtension((new Extension('opcache'))->withOptions('--enable-opcache'));

$p->addExtension(
    (new Extension('xml'))
        ->withOptions('--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml')
);

$p->addExtension(
    (new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg=/usr --with-freetype=/usr')
);

$p->addExtension(
    (new Extension('swoole'))
        ->withOptions('--enable-swoole --enable-sockets --enable-mysqlnd --enable-http2 --enable-swoole-json --enable-swoole-curl --enable-cares')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
);

$p->addExtension((new Extension('redis'))
    ->withOptions('--enable-redis')
    ->withPeclVersion('5.3.5')
    ->withHomePage('https://github.com/phpredis/phpredis')
    ->withLicense('https://github.com/phpredis/phpredis/blob/develop/COPYING', Extension::LICENSE_PHP)
);

$p->addExtension((new Extension('imagick'))
    ->withOptions('--with-imagick=/usr/imagemagick')
    ->withPeclVersion('3.6.0')
    ->withHomePage('https://github.com/Imagick/imagick')
    ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
);

$p->addExtension((new Extension('yaml'))
    ->withOptions('--with-yaml=/usr/libyaml')
    ->withPeclVersion('2.2.2')
    ->withHomePage('https://github.com/php/pecl-file_formats-yaml')
    ->withLicense('https://github.com/php/pecl-file_formats-yaml/blob/php7/LICENSE', Extension::LICENSE_MIT)
);

$p->gen();
$p->info();

$endCallback($p);
