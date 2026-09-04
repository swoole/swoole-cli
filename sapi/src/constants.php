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
define("MPFR_PREFIX", $p->getGlobalPrefix() . '/mpfr');
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

// curl 对 5xx/408/429 等"暂态"HTTP 错误也会按 --retry 重试。
// 服务端故障（如镜像 502）重试价值低，5 次会白等约 100 秒；
// 降到 2 次，保留对瞬时网络错误的容忍，同时缩短失败等待。
const DOWNLOAD_FILE_RETRY_NUMBE = 2;
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
define("LIBPSL_PREFIX", $p->getGlobalPrefix() . '/libpsl');

define("LIBHEIF_PREFIX", $p->getGlobalPrefix() . '/libheif');
define("LIBX265_PREFIX", $p->getGlobalPrefix() . '/libx265');
define("LIBDE265_PREFIX", $p->getGlobalPrefix() . '/libde265');
define("OPENH264_PREFIX", $p->getGlobalPrefix() . '/openh264');
define("LIBTIFF_PREFIX", $p->getGlobalPrefix() . '/libtiff');
define("LIBRAW_PREFIX", $p->getGlobalPrefix() . '/libraw');
define("LCMS2_PREFIX", $p->getGlobalPrefix() . '/lcms2');
define("OPENJPEG_PREFIX", $p->getGlobalPrefix() . '/openjpeg');
define("LIBDEFLATE_PREFIX", $p->getGlobalPrefix() . '/libdeflate');
define("LIBJXL_PREFIX", $p->getGlobalPrefix() . '/libjxl');

