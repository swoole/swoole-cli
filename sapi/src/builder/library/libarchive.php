<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libarchive_prefix = LIBARCHIVE_PREFIX;

    $openssl_prefix = OPENSSL_PREFIX;
    $libb2_prefix = LIBB2_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX ;
    $zlib_prefix = ZLIB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $libexpat_prefix = LIBEXPAT_PREFIX;
    $pcre_prefix = PCRE_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;

    $p->addLibrary(
        (new Library('libarchive'))
            ->withHomePage('https://github.com/libarchive/libarchive.git')
            ->withManual('https://www.libarchive.org/')
            ->withManual('https://github.com/libarchive/libarchive/wiki/BuildInstructions')
            ->withLicense('https://github.com/libarchive/libarchive/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withFile('libarchive-latest.tar.gz')
            ->withDownloadScript(
                'libarchive',
                <<<EOF
            git clone -b master --depth=1  https://github.com/libarchive/libarchive.git
EOF
            )
            ->withPreInstallCommand(
                'debian',
                <<<EOF
              # apt install groff  util-linux
EOF
            )
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
              # apk add groff  util-linux
EOF
            )
            ->withPrefix($libarchive_prefix)
            //->withBuildCached(false)
           ->withCleanPreInstallDirectory($libarchive_prefix)
            /*
            ->withConfigure(
                <<<EOF

                sh build/autogen.sh
                ./configure --help

                PACKAGES=" openssl gmp libxml-2.0 liblz4 liblzma zlib libzstd nettle expat"
                PACKAGES=" \$PACKAGES  libpcre2-16 libpcre2-32 libpcre2-8  libpcre2-posix"
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$bzip2_prefix}/include -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include " \
                LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib" \
                LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lbz2 -liconv " \
                LIBSREQUIRED=" \$PACKAGES " \
                LIBS=" \$LIBS " \
                ./configure \
                --prefix={$libarchive_prefix} \
                --enable-shared=no \
                --enable-static=yes \
                --with-nettle \
                --with-openssl \
                --with-xml2 \
                --with-lz4 \
                --with-zstd \
                --with-lzma \
                --with-bz2lib \
                --with-zlib \
                --with-libiconv-prefix={$libiconv_prefix} \
                --with-expat \
                --enable-posix-regex-lib=libpcre2posix \
                --with-openssl \
                --without-mbedtls \
                --enable-bsdcpio=static \
                --enable-bsdtar=static \
                --enable-bsdunzip=static
EOF
            )
           */
                ->withBuildCached(false)
                ->withBuildScript(<<<EOF
                mkdir -p build
                cd build

                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$libarchive_prefix} \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON \
                -DCMAKE_MACOSX_RPATH="{$openssl_prefix};{$libzstd_prefix};{$libxml2_prefix};{$libexpat_prefix};{$pcre_prefix};"  \
                -DENABLE_MBEDTLS=OFF  \
                -DENABLE_NETTL=ON  \
                -DENABLE_OPENSS=ON  \
                -DENABLE_LIBB2=ON  \
                -DENABLE_LZ4=ON  \
                -DENABLE_LZO=OFF  \
                -DENABLE_LZMA=ON  \
                -DENABLE_ZSTD=ON  \
                -DENABLE_ZLIB=ON  \
                -DENABLE_BZip2=ON  \
                -DENABLE_LIBXML2=ON  \
                -DENABLE_EXPAT=ON  \
                -DENABLE_PCREPOSIX=ON  \
                -DENABLE_PCRE2POSIX=OFF  \
                -DENABLE_LIBGCC=OFF  \
                -DENABLE_CNG=OFF  \
                -DENABLE_TAR=ON  \
                -DENABLE_TAR_SHARED=OFF  \
                -DENABLE_CPIO=ON  \
                -DENABLE_CPIO_SHARED=OFF  \
                -DENABLE_CAT=ON  \
                -DENABLE_CAT_SHARED=OFF  \
                -DENABLE_UNZIP=OFF  \
                -DENABLE_ACL=OFF  \
                -DENABLE_ICONV=ON  \
                -DENABLE_TEST=OFF \
                -DBZip2_ROOT={$bzip2_prefix} \
                -DICONV_ROOT={$libiconv_prefix} \
                -DLZ4_ROOT={$liblz4_prefix} \
                -DLZMA_ROOT={$liblzma_prefix} \
                -DZLIB_ROOT={$zlib_prefix} \
                -DLIBB2_ROOT={$libb2_prefix}

                cmake --build . --config Release
                cmake --build . --config Release --target install



EOF
)
            /*
            ->withScriptAfterInstall(
                <<<EOF
            LINE_NUMBER=$(grep -n 'Requires.private:' {$libarchive_prefix}/lib/pkgconfig/libarchive.pc |cut -d ':' -f 1)
            sed -i.save "\${LINE_NUMBER} s/iconv//" {$libarchive_prefix}/lib/pkgconfig/libarchive.pc

            DEST_LINE="-L{$libxml2_prefix}/libxml2/lib -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib"
            sed -i.save "s@-L{$libxml2_prefix}/lib@\$DEST_LINE@" {$libarchive_prefix}/lib/pkgconfig/libarchive.pc


EOF
            )
            */
            ->withPkgName('libarchive')
            ->withBinPath($libarchive_prefix . '/bin/')
            ->withDependentLibraries(
                'openssl',
                'libb2',
                'liblz4',
                'liblzma',
                'libzstd',
                'bzip2',
                'zlib',
                'libxml2',
                'libexpat',
                'pcre',
                'libiconv',

                'nettle',
                'gmp'
            )
    );
};
