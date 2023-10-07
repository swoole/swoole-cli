<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $gnutls_prefix = GNUTLS_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libev_prefix = LIBEV_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('gnutls');
    $lib->withHomePage('https://www.gnutls.org/')
        ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://www.gnupg.org/ftp/gcrypt/gnutls/v3.8/gnutls-3.8.1.tar.xz')
        ->withHttpProxy(false)
        ->withManual('https://gitlab.com/gnutls/gnutls.git')
        ->withManual('https://www.gnutls.org/download.html')
        ->withPrefix($gnutls_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($gnutls_prefix)
        ->withConfigure(
            <<<EOF

:<<'CMD_EOF'
                export GMP_CFLAGS=$(pkg-config  --cflags --static gmp)
                export GMP_LIBS=$(pkg-config    --libs   --static gmp)
                export LIBTASN1_CFLAGS=$(pkg-config  --cflags --static libtasn1)
                export LIBTASN1_LIBS=$(pkg-config    --libs   --static libtasn1)

                export LIBIDN2_CFLAGS=$(pkg-config  --cflags --static libidn2)
                export LIBIDN2_LIBS=$(pkg-config    --libs   --static libidn2)

                export LIBZSTD_CFLAGS=$(pkg-config  --cflags --static libzstd)
                export LIBZSTD_LIBS=$(pkg-config    --libs   --static libzstd)
                export NETTLE_CFLAGS=$(pkg-config  --cflags --static nettle)
                export NETTLE_LIBS=$(pkg-config    --libs   --static nettle)
                export LIBIDN2_CFLAGS=$(pkg-config  --cflags --static libidn2)
                export LIBIDN2_LIBS=$(pkg-config    --libs   --static libidn2)

                # export P11_KIT_CFLAGS=$(pkg-config  --cflags --static p11-kit-1)
                # export P11_KIT_LIBS=$(pkg-config    --libs   --static p11-kit-1)
CMD_EOF

                ./configure --help

                PACKAGES="nettle hogweed openssl  gmp libzstd libbrotlicommon libbrotlidec libbrotlienc"
                PACKAGES="\$PACKAGES libidn2 libtasn1 libunbound "
                PACKAGES="\$PACKAGES hiredis libnghttp2 "

                export LIBBROTLIENC_CFLAGS=$(pkg-config  --cflags --static libbrotlienc libbrotlicommon)
                export LIBBROTLIENC_LIBS=$(pkg-config    --libs   --static libbrotlienc libbrotlicommon)

                export LIBBROTLIDEC_CFLAGS=$(pkg-config  --cflags --static libbrotlidec libbrotlicommon)
                export LIBBROTLIDEC_LIBS=$(pkg-config    --libs   --static libbrotlidec libbrotlicommon)

                CPPFLAGS="$(pkg-config --cflags-only-I --static \$PACKAGES ) -I{$libunistring_prefix}/include " \
                LDFLAGS="$(pkg-config --libs-only-L --static \$PACKAGES ) -L{$libunistring_prefix}/lib/" \
                LIBS="$(pkg-config --libs-only-l --static \$PACKAGES ) -lunistring" \
                ./configure \
                --prefix={$gnutls_prefix} \
                --enable-static=yes \
                --enable-shared=no \
                --with-zstd \
                --with-brotli \
                --with-libiconv-prefix={$iconv_prefix} \
                --with-libz-prefix={$zlib_prefix} \
                --with-nettle-mini \
                --with-libintl-prefix={$gettext_prefix}  \
                --enable-libdane \
                --without-tpm2 \
                --without-tpm \
                --disable-doc \
                --disable-tests \
                --disable-full-test-suite \
                --disable-valgrind-tests \
                --enable-openssl-compatibility \
                --without-p11-kit \
                --without-libseccomp-prefix \
                --without-libcrypto-prefix \
                --without-librt-prefix \
                --without-libdl-prefix \
                --with-included-unistring

                # --with-included-unistring \
                # --with-included-libtasn1 \
                # --with-libev-prefix={$libev_prefix} \
EOF
        )
        //->withPkgName('gnutls')
        ->withDependentLibraries(
            'brotli',
            'zlib',
            'gmp',
            'nettle',
            'libzstd',
            'libiconv',
            'openssl',
            'libidn2',
            'libunistring',
            'gettext',
            'unbound',
            //'libev',
            'libtasn1',
            'nghttp2',
            'hiredis'

        )
    ;

    $p->addLibrary($lib);


};
