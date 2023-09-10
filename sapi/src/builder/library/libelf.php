<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    //elfutils  实用程序和库的集合，用于读取、创建和修改 ELF 二进制文件

    $libelf_prefix = LIBELF_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;
    $p->addLibrary(
        (new Library('libelf'))
            ->withHomePage('http://elfutils.org/')
            ->withLicense('https://chromium.googlesource.com/external/elfutils/+/refs/heads/master/COPYING-LGPLV3', Library::LICENSE_GPL)
            ->withManual('https://sourceware.org/git/?p=elfutils.git;a=summary')
            ->withManual('https://chromium.googlesource.com/external/elfutils/+/refs/heads/master/README')
            ->withHttpProxy(true, true)
            ->withFile('elfutils-0.189.tar.gz')
            ->withDownloadScript(
                'elfutils',
                <<<EOF
                # 查看tag  https://sourceware.org/git/?p=elfutils.git;a=summary

                git clone -b elfutils-0.189 --depth=1  git://sourceware.org/git/elfutils.git

EOF
            )
            ->withPrefix($libelf_prefix)
            ->withPreInstallCommand(
                'debian',
                <<<EOF
            # apt install -y autopoint elfutils

        EOF
            )
            ->withPreInstallCommand('alpine', <<<EOF
                apk add argp-standalone  # https://github.com/ericonr/argp-standalone.git
                apk add musl-fts-dev     # https://github.com/void-linux/musl-fts.git
                apk add musl-obstack-dev  # https://github.com/void-linux/musl-obstack.git
                apk add gawk
                apk add binutils binutils-dev

                apk add gettext-dev gettext-static
                apk add musl-libintl

        EOF
            )
            //->withAutoUpdateFile()
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

            # 读取环境变量,判定是静态编译  BUILD_STATIC_TRUE BUILD_STATIC_FALSE
            # BUILD_STATIC

            autoreconf -if
            ./configure --help

            #  CFLAGS=" -std=gnu99 -static -g -fPIE -fPIC -O2 -Wall   " \
            #  -I{$gettext_prefix}/include
            #  -L{$gettext_prefix}/lib
            # -lintl

            BUILD_STATIC=true
            BUILD_STATIC_FALSE="#"
            BUILD_STATIC_TRUE=""


            PACKAGES=" sqlite3 libcurl libarchive libcares "
            PACKAGES=" libbrotlicommon libbrotlidec  libbrotlienc"
            PACKAGES=" libzstd"
            PACKAGES=" libnghttp2 libnghttp3 libngtcp2 libngtcp2_crypto_openssl"
            PACKAGES=" nettle"
            PACKAGES=" liblzma"
            PACKAGES=" liblz4"
            PACKAGES=" libzstd"
            PACKAGES=" gmp"
            PACKAGES=" zlib"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib  -static --static " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lm -pthread  -liconv " \
            ./configure \
            --prefix={$libelf_prefix} \
            --enable-install-elfh \
            --with-zlib \
            --with-bzlib \
            --without-lzma \
            --without-zstd \
            --without-biarch \
            --without-valgrind \
            --enable-maintainer-mode \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libintl-prefix={$gettext_prefix} \
            --disable-nls \
            --disable-debuginfod  \
            --disable-libdebuginfod \
            --enable-gprof

EOF
            )
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
                'libzstd',
                'liblz4',
                'bzip2',
                'gmp',
                //'gettext',
                "zlib"
            )
    );
};

/*
 * undefined reference to libintl_gettext
 * https://www.gnu.org/software/gettext/FAQ.html
 *
 *  nm /usr/local/swoole-cli/gettext/lib/libintl.a |  grep libintl_gettext
 */

/*
 * 参考
 * https://git.alpinelinux.org/aports/tree/main/elfutils/APKBUILD
 */
