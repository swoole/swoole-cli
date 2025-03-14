<?php

use SwooleCli\Preprocessor;

$p = Preprocessor::getInstance();
define("JPEG_PREFIX", $p->getGlobalPrefix() . '/libjpeg');
define("GIF_PREFIX", $p->getGlobalPrefix() . '/libgif');
define("ZIP_PREFIX", $p->getGlobalPrefix() . '/libzip');
define("ZLIB_PREFIX", $p->getGlobalPrefix() . '/zlib');
define("BZIP2_PREFIX", $p->getGlobalPrefix() . '/bzip2');
define("FREETYPE_PREFIX", $p->getGlobalPrefix() . '/freetype');
define("PNG_PREFIX", $p->getGlobalPrefix() . '/libpng');
define("WEBP_PREFIX", $p->getGlobalPrefix() . '/libwebp');
define("CURL_PREFIX", $p->getGlobalPrefix() . '/curl');
define("CARES_PREFIX", $p->getGlobalPrefix() . '/cares');
define("OPENSSL_PREFIX", $p->getGlobalPrefix() . '/openssl');
define("GMP_PREFIX", $p->getGlobalPrefix() . '/gmp');
define("ICONV_PREFIX", $p->getGlobalPrefix() . '/libiconv');
define("IMAGEMAGICK_PREFIX", $p->getGlobalPrefix() . '/imagemagick');
define("ICU_PREFIX", $p->getGlobalPrefix() . '/icu');
define("ONIGURUMA_PREFIX", $p->getGlobalPrefix() . '/oniguruma');
define("MIMALLOC_PREFIX", $p->getGlobalPrefix() . '/mimalloc');
define("NCURSES_PREFIX", $p->getGlobalPrefix() . '/ncurses');
define("READLINE_PREFIX", $p->getGlobalPrefix() . '/readline');
define("LIBYAML_PREFIX", $p->getGlobalPrefix() . '/libyaml');
define("LIBXML2_PREFIX", $p->getGlobalPrefix() . '/libxml2');
define("LIBXSLT_PREFIX", $p->getGlobalPrefix() . '/libxslt');
define("SQLITE3_PREFIX", $p->getGlobalPrefix() . '/sqlite3');
define("LIBSODIUM_PREFIX", $p->getGlobalPrefix() . '/libsodium');
define("LIBEDIT_PREFIX", $p->getGlobalPrefix() . '/libedit');
define("BROTLI_PREFIX", $p->getGlobalPrefix() . '/brotli');

define("LIBLZ4_PREFIX", $p->getGlobalPrefix() . '/liblz4');
define("LIBLZMA_PREFIX", $p->getGlobalPrefix() . '/liblzma');
define("LIBZSTD_PREFIX", $p->getGlobalPrefix() . '/libzstd');
define("LIBXLSXWRITER_PREFIX", $p->getGlobalPrefix() . '/libxlsxwriter');
define("LIBMCRYPT_PREFIX", $p->getGlobalPrefix() . '/libmcrypt');
define("BISON_PREFIX", $p->getGlobalPrefix() . '/bison');
define("NGHTTP2_PREFIX", $p->getGlobalPrefix() . '/nghttp2');

define("LIBIDN2_PREFIX", $p->getGlobalPrefix() . '/libidn2');


const DOWNLOAD_FILE_RETRY_NUMBE = 5;
const DOWNLOAD_FILE_WAIT_RETRY = 5;
const DOWNLOAD_FILE_USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';

const DOWNLOAD_FILE_CONNECTION_TIMEOUT = 15;

define("NGHTTP3_PREFIX", $p->getGlobalPrefix() . '/nghttp3');
define("NGTCP2_PREFIX", $p->getGlobalPrefix() . '/ngtcp2');
define("LIBSSH2_PREFIX", $p->getGlobalPrefix() . '/libssh2');

define("PGSQL_PREFIX", $p->getGlobalPrefix() . '/pgsql');
define("UNIX_ODBC_PREFIX", $p->getGlobalPrefix() . '/unix_odbc');

define("UTIL_LINUX_PREFIX", $p->getGlobalPrefix() . '/util_linux');
define("GETTEXT_PREFIX", $p->getGlobalPrefix() . '/gettext');
define("LIBUNISTRING_PREFIX", $p->getGlobalPrefix() . '/libunistring');
define("LIBURING_PREFIX", $p->getGlobalPrefix() . '/liburing');

define("LIBAVIF_PREFIX", $p->getGlobalPrefix() . '/libavif');
define("DAV1D_PREFIX", $p->getGlobalPrefix() . '/dav1d');
define("LIBGAV1_PREFIX", $p->getGlobalPrefix() . '/libgav1');
define("AOM_PREFIX", $p->getGlobalPrefix() . '/aom');
define("SVT_AV1_PREFIX", $p->getGlobalPrefix() . '/svt_av1');
define("LIBYUV_PREFIX", $p->getGlobalPrefix() . '/libyuv');

define("PYTHON3_PREFIX", $p->getGlobalPrefix() . '/python3');
define("LIBMPDECIMAL_PREFIX", $p->getGlobalPrefix() . '/libmpdecimal');
define("LIBB2_PREFIX", $p->getGlobalPrefix() . '/libb2');
define("LIBEXPAT_PREFIX", $p->getGlobalPrefix() . '/libexpat');

define("ZOOKEEPER_PREFIX", $p->getGlobalPrefix() . '/zookeeper');
define("LIBEVENT_PREFIX", $p->getGlobalPrefix() . '/libevent');
define("SNAPPY_PREFIX", $p->getGlobalPrefix() . '/snappy');
define("LIBSASL_PREFIX", $p->getGlobalPrefix() . '/sasl');

define("LIBTIFF_PREFIX", $p->getGlobalPrefix() . '/libtiff');
define("LIBRAW_PREFIX", $p->getGlobalPrefix() . '/libraw');
define("LCMS2_PREFIX", $p->getGlobalPrefix() . '/lcms2');

define("ABSL_PREFIX", $p->getGlobalPrefix() . '/absl');
define("RAV1E_PREFIX", $p->getGlobalPrefix() . '/rav1e');
define("SDL2_PREFIX", $p->getGlobalPrefix() . '/sdl2');
define("LIBARGON2_PREFIX", $p->getGlobalPrefix() . '/libargon2');

define("MUSL_CROSS_MAKE_PREFIX", $p->getGlobalPrefix() . '/musl_cross_make');

define("GRPC_PREFIX", $p->getGlobalPrefix() . '/grpc');
define("RE2_PREFIX", $p->getGlobalPrefix() . '/re2');
define("LIBFFI_PREFIX", $p->getGlobalPrefix() . '/libffi');

define("LIBMEMCACHED_PREFIX", $p->getGlobalPrefix() . '/libmemcached');
define("LIBMEMCACHED_AWESOME_PREFIX", $p->getGlobalPrefix() . '/libmemcached_awesome');

define("EXAMPLE_PREFIX", $p->getGlobalPrefix() . '/example');


