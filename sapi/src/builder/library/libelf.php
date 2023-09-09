<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    // 实用程序和库的集合，用于读取、创建和修改 ELF 二进制文件，
    $libelf_prefix = LIBELF_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;

    $p->addLibrary(
        (new Library('libelf'))
            ->withHomePage('http://elfutils.org/')
            ->withLicense('https://chromium.googlesource.com/external/elfutils/+/refs/heads/master/COPYING-LGPLV3', Library::LICENSE_GPL)
            //->withFile('elfutils-0.178.tar.gz')
            ->withFile('elfutils-latest.tar.gz')
            ->withManual('https://sourceware.org/git/?p=elfutils.git;a=summary')
            ->withManual('https://chromium.googlesource.com/external/elfutils/+/refs/heads/master/README')
            ->withHttpProxy(true, true)
            ->withDownloadScript(
                'elfutils',
                <<<EOF

                # git clone -b elfutils-0.178 https://chromium.googlesource.com/external/elfutils
                git clone --depth=1  git://sourceware.org/git/elfutils.git
EOF
            )
            ->withPrefix($libelf_prefix)
            ->withPreInstallCommand(
                'debian',
                <<<EOF
            # apt install -y autopoint elfutils
EOF
            )
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

            # 读取环境变量,判定是静态编译  BUILD_STATIC_TRUE BUILD_STATIC_FALSE
            # BUILD_STATIC

            autoreconf -if
            ./configure --help

            PACKAGES=" sqlite3 libcurl libarchive libcares "
            PACKAGES=" libbrotlicommon libbrotlidec  libbrotlienc"
            PACKAGES=" libzstd"
            PACKAGES=" libnghttp2 libnghttp3 libngtcp2 libngtcp2_crypto_openssl"
            PACKAGES=" nettle"
            PACKAGES=" liblzma"
            PACKAGES=" liblz4"
            PACKAGES=" gmp"
            PACKAGES=" zlib"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib -static --static " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lm -pthread " \
            CFLAGS=" -std=gnu11 -static -g -fPIE -fPIC -O2 -Wall   " \
            BUILD_STATIC=true \
            ./configure \
            --prefix={$libelf_prefix} \
            --enable-install-elfh \
            --without-valgrind \
            --with-zlib \
            --with-bzlib \
            --without-lzma \
            --with-zstd \
            --without-biarch \
            --with-libiconv-prefix={$libiconv_prefix}

            # --enable-maintainer-mode \
EOF
            )
            ->withMakeOptions('all')
            //->withMakeInstallCommand('install-local')
            ->withPkgName('libelf')
            ->withDependentLibraries(
                'libarchive',
                'sqlite3',
                'curl',
                'libiconv',
                'cares',
                'brotli',
                'libzstd',
                'nghttp2',
                'nghttp3',
                'ngtcp2',
                'nettle',
                'liblzma',
                'liblz4',
                'bzip2',
                'gmp',
                'gettext',
                "zlib"
            )
    );
};
