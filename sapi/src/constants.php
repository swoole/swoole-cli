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
define("LIBTIFF_PREFIX", $p->getGlobalPrefix() . '/libtiff');
define("LIBRAW_PREFIX", $p->getGlobalPrefix() . '/libraw');
define("LCMS2_PREFIX", $p->getGlobalPrefix() . '/lcms2');


define("CURL_PREFIX", $p->getGlobalPrefix() . '/curl');
define("CARES_PREFIX", $p->getGlobalPrefix() . '/cares');
define("OPENSSL_PREFIX", $p->getGlobalPrefix() . '/openssl');
define("OPENSSL_v1_PREFIX", $p->getGlobalPrefix() . '/openssl_v1');

define("LIBGCRYPT_PREFIX", $p->getGlobalPrefix() . '/libgcrypt');
define("LIBGCRYPT_ERROR_PREFIX", $p->getGlobalPrefix() . '/libgcrypt_error');

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

define("BISON_PREFIX", $p->getGlobalPrefix() . '/bison');
define("NGHTTP2_PREFIX", $p->getGlobalPrefix() . '/nghttp2');

define("LIBFFI_PREFIX", $p->getGlobalPrefix() . '/libffi');

define("LIBEV_PREFIX", $p->getGlobalPrefix() . '/libev');
define("LIBUV_PREFIX", $p->getGlobalPrefix() . '/libuv');

define("LIBXLSXWRITER_PREFIX", $p->getGlobalPrefix() . '/libxlsxwriter');
define("LIBMINZIP_PREFIX", $p->getGlobalPrefix() . '/libminizip');
define("LIBXLSXIO_PREFIX", $p->getGlobalPrefix() . '/libxlsxio');
define("LIBEXPAT_PREFIX", $p->getGlobalPrefix() . '/libexpat');
define("LIBMCRYPT_PREFIX", $p->getGlobalPrefix() . '/libmcrypt');
define("PRIVOXY_PREFIX", $p->getGlobalPrefix() . '/privoxy');


//  test
define("LIBDE265_PREFIX", $p->getGlobalPrefix() . '/libde265');
define("LIBX264_PREFIX", $p->getGlobalPrefix() . '/libx264');
define("NUMA_PREFIX", $p->getGlobalPrefix() . '/numa');
define("LIBX265_PREFIX", $p->getGlobalPrefix() . '/libx265');
define("SVT_AV1_PREFIX", $p->getGlobalPrefix() . '/svt_av1');
define("LIBHEIF_PREFIX", $p->getGlobalPrefix() . '/libheif');
define("LIBJXL_PREFIX", $p->getGlobalPrefix() . '/libjxl');
define("LIBGD_PREFIX", $p->getGlobalPrefix() . '/libgd');
define("DAV1D_PREFIX", $p->getGlobalPrefix() . '/dav1d');
define("LIBGAV1_PREFIX", $p->getGlobalPrefix() . '/libgav1');
define("RAV1E_PREFIX", $p->getGlobalPrefix() . '/rav1e');
define("AOM_PREFIX", $p->getGlobalPrefix() . '/aom');
define("LIBAVIF_PREFIX", $p->getGlobalPrefix() . '/libavif');
define("NASM_PREFIX", $p->getGlobalPrefix() . '/nasm');
define("LIBYUV_PREFIX", $p->getGlobalPrefix() . '/libyuv');
define("DEPOT_TOOLS_PREFIX", $p->getGlobalPrefix() . '/depot_tools');


define("HARFBUZZ_PREFIX", $p->getGlobalPrefix() . '/harfbuzz');
define("LIBFRIBIDI_PREFIX", $p->getGlobalPrefix() . '/libfribidi');


define("JANSSON_PREFIX", $p->getGlobalPrefix() . '/jansson');
define("NETTLE_PREFIX", $p->getGlobalPrefix() . '/nettle');
define("LIBTASN1_PREFIX", $p->getGlobalPrefix() . '/libtasn1');

define("GNUTLS_PREFIX", $p->getGlobalPrefix() . '/gnutls');

define("OPENCV_PREFIX", $p->getGlobalPrefix() . '/opencv');


define("GNUPG_PREFIX", $p->getGlobalPrefix() . '/gnupg');
define("BOOST_PREFIX", $p->getGlobalPrefix() . '/boost');
define("BORINGSSL_PREFIX", $p->getGlobalPrefix() . '/boringssl');
define("WOLFSSL_PREFIX", $p->getGlobalPrefix() . '/wolfssl');
define("VALGRIND_PREFIX", $p->getGlobalPrefix() . '/valgrind');
define("CAPSTONE_PREFIX", $p->getGlobalPrefix() . '/capstone');
define("DYNASM_PREFIX", $p->getGlobalPrefix() . '/dynasm');


define("NGINX_PREFIX", $p->getGlobalPrefix() . '/nginx');
define("PCRE2_PREFIX", $p->getGlobalPrefix() . '/pcre2');
define("PCRE_PREFIX", $p->getGlobalPrefix() . '/pcre');

define("LIBIDN2_PREFIX", $p->getGlobalPrefix() . '/libidn2');

const DOWNLOAD_FILE_RETRY_NUMBE = 5;
const DOWNLOAD_FILE_WAIT_RETRY = 5;
const DOWNLOAD_FILE_USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';

const DOWNLOAD_FILE_CONNECTION_TIMEOUT = 15;

define("NGHTTP3_PREFIX", $p->getGlobalPrefix() . '/nghttp3');
define("NGTCP2_PREFIX", $p->getGlobalPrefix() . '/ngtcp2');
define("LIBSSH2_PREFIX", $p->getGlobalPrefix() . '/libssh2');

define("FFMPEG_PREFIX", $p->getGlobalPrefix() . '/ffmpeg');
define("NODEJS_PREFIX", $p->getGlobalPrefix() . '/nodejs');
define("GOLANG_PREFIX", $p->getGlobalPrefix() . '/golang');


define("XORG_MACROS_PREFIX", $p->getGlobalPrefix() . '/xorg_macros');
define("XORGPROTO_PREFIX", $p->getGlobalPrefix() . '/xorgproto');
define("LIBX11_PREFIX", $p->getGlobalPrefix() . '/libX11');
define("LIBXPM_PREFIX", $p->getGlobalPrefix() . '/libXpm');
define("GRAPHVIZ_PREFIX", $p->getGlobalPrefix() . '/graphviz');
define("MUSL_LIBC_PREFIX", $p->getGlobalPrefix() . '/musl_libc');
define("MUSL_CROSS_MAKE_PREFIX", $p->getGlobalPrefix() . '/musl_cross_make');
define('LIBRSVG_PREFIX', $p->getGlobalPrefix() . '/librsvg');

define('GETTEXT_PREFIX', $p->getGlobalPrefix() . '/gettext');
define('LIBINTL_PREFIX', $p->getGlobalPrefix() . '/libintl');


define("LIBUNISTRING_PREFIX", $p->getGlobalPrefix() . '/libunistring');

define("PGSQL_PREFIX", $p->getGlobalPrefix() . '/pgsql');

define("UNIX_ODBC_PREFIX", $p->getGlobalPrefix() . '/unixODBC');
define("ZOOKEEPER_PREFIX", $p->getGlobalPrefix() . '/zookeeper');
define("LIBEVENT_PREFIX", $p->getGlobalPrefix() . '/libevent');

define("SNAPPY_PREFIX", $p->getGlobalPrefix() . '/snappy');
define("LIBSASL_PREFIX", $p->getGlobalPrefix() . '/sasl');



define("LIBARCHIVE_PREFIX", $p->getGlobalPrefix() . '/libarchive');

define("SOCAT_PREFIX", $p->getGlobalPrefix() . '/socat');
define("ARIA2_PREFIX", $p->getGlobalPrefix() . '/aria2');

define("OVS_PREFIX", $p->getGlobalPrefix() . '/ovs');
define("OVN_PREFIX", $p->getGlobalPrefix() . '/ovn');
define("DPDK_PREFIX", $p->getGlobalPrefix() . '/dpdk');

define("COTURN_PREFIX", $p->getGlobalPrefix() . '/coturn');
define("HIREDIS_PREFIX", $p->getGlobalPrefix() . '/hiredis');
define("LIBMICROHTTP_PREFIX", $p->getGlobalPrefix() . '/libmicrohttp');

define("ABSL_PREFIX", $p->getGlobalPrefix() . '/absl');
define("VTK_PREFIX", $p->getGlobalPrefix() . '/vtk');
define("JEMALLOC_PREFIX", $p->getGlobalPrefix() . '/jemalloc');
define("TCMALLOC_PREFIX", $p->getGlobalPrefix() . '/tcmalloc');

define("LIBSHARPYUV_PREFIX", $p->getGlobalPrefix() . '/libsharpyuv');
define("LIBWEBSOCKETS_PREFIX", $p->getGlobalPrefix() . '/libwebsockets');
define("LIBOPENEXR_PREFIX", $p->getGlobalPrefix() . '/libOpenEXR');
define("GSTREAMER_PREFIX", $p->getGlobalPrefix() . '/gstreamer');
define("LIBNICE_PREFIX", $p->getGlobalPrefix() . '/libnice');
define("LIBSRTP_PREFIX", $p->getGlobalPrefix() . '/libsrtp');
define("LIBUSRSCTP_PREFIX", $p->getGlobalPrefix() . '/libusrsctp');
define("LIBMICROHTTPD_PREFIX", $p->getGlobalPrefix() . '/libmicrohttpd');
define("JANUS_GATEWAY_PREFIX", $p->getGlobalPrefix() . '/janus_gateway');
define("FREESWITCH_PREFIX", $p->getGlobalPrefix() . '/freeswitch');
define("LIBBPF_PREFIX", $p->getGlobalPrefix() . '/libbpf');
define("LIBELF_PREFIX", $p->getGlobalPrefix() . '/libelf');
