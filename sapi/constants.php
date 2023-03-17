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

