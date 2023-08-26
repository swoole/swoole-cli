<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libarchive_prefix = LIBARCHIVE_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $p->addLibrary(
        (new Library('libarchive'))
            ->withHomePage('https://github.com/libarchive/libarchive.git')
            ->withManual('https://www.libarchive.org/')
            ->withManual('https://github.com/libarchive/libarchive/wiki/BuildInstructions')
            ->withLicense('https://github.com/libarchive/libarchive/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withFile('libarchive-latest.tar.gz')
            ->withDownloadScript('libarchive',<<<EOF
            git clone -b master --depth=1  https://github.com/libarchive/libarchive.git
EOF
            )
            ->withPreInstallCommand(
                'debian',
                <<<EOF
              apt install groff  util-linux
EOF
            )
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
              apk add groff  util-linux
EOF
            )
            ->withPrefix($libarchive_prefix)
            /*
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libarchive_prefix)
            ->withBuildLibraryCached(false)
            */
            ->withConfigure(
                <<<EOF

                sh build/autogen.sh
                ./configure --help
                PACKAGES=" openssl gmp libxml-2.0 liblz4 liblzma zlib libzstd nettle"
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$bzip2_prefix}/include -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include " \
                LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib" \
                LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lbz2 -liconv " \
                LIBS="\$LDFLAGS \$LIBS" \
                ./configure \
                --prefix={$libarchive_prefix} \
                --enable-shared=no \
                --enable-static=yes \
                --enable-bsdcpio=static \
                --enable-bsdtar=static \
                --with-nettle \
                --with-openssl \
                --with-xml2 \
                --with-lz4 \
                --with-zstd \
                --with-lzma \
                --with-bz2lib \
                --with-zlib \
                --with-libiconv-prefix={$libiconv_prefix} \
                --without-mbedtls
EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            LINE_NUMBER=$(grep -n 'Requires.private:' {$libarchive_prefix}/lib/pkgconfig/libarchive.pc |cut -d ':' -f 1)
            sed -i.save "\${LINE_NUMBER} s/iconv//" {$libarchive_prefix}/lib/pkgconfig/libarchive.pc
EOF
            )
            ->withPkgName('libarchive')
            ->withBinPath($libarchive_prefix . '/bin/')
            ->withDependentLibraries(
                'openssl',
                'libxml2',
                'zlib',
                'liblzma',
                'liblz4',
                'libiconv',
                'libzstd',
                'bzip2',
                'nettle',
                'bzip2',
                'libiconv',
                'gmp'
            )
    );
};
