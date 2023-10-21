<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    //elfutils  实用程序和库的集合，用于读取、创建和修改 ELF 二进制文件

    $libelf_prefix = LIBELF_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    $ldflags  = $p->getOsType() == 'macos' ? ' ' : ' -static --static ';

    $p->addLibrary(
        (new Library('libelf'))
            ->withHomePage('http://elfutils.org/')
            ->withLicense('https://chromium.googlesource.com/external/elfutils/+/refs/heads/master/COPYING-LGPLV3', Library::LICENSE_GPL)
            ->withManual('https://sourceware.org/git/?p=elfutils.git;a=summary')
            ->withManual('https://git.alpinelinux.org/aports/tree/main/elfutils/APKBUILD')
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
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
                apk add argp-standalone  # https://github.com/ericonr/argp-standalone.git
                apk add musl-fts-dev     # https://github.com/void-linux/musl-fts.git
                apk add musl-obstack-dev  # https://github.com/void-linux/musl-obstack.git
                apk add gawk

                # apk add binutils binutils-dev
                # apk add gettext-dev gettext-static
                # apk add musl-libintl

                # apk add libelf-static

        EOF
            )
            //->withAutoUpdateFile()
            ->withBuildCached(false)
            //->withCleanPreInstallDirectory($libelf_prefix)
            ->withBuildScript(
                <<<EOF
            autoreconf -if
            ./configure --help

            PACKAGES=" libarchive openssl libxml-2.0   "
            PACKAGES="\$PACKAGES libbrotlicommon libbrotlidec libbrotlienc "
            PACKAGES="\$PACKAGES zlib liblzma liblz4 libzstd "
            PACKAGES="\$PACKAGES libnghttp2 libnghttp3 libngtcp2 libngtcp2_crypto_quictls libssh2 libcurl "
            PACKAGES="\$PACKAGES nettle hogweed gmp sqlite3  libcares ncursesw "
            PACKAGES="\$PACKAGES libmicrohttpd "

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) "
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) "

            CPPFLAGS="\$CPPFLAGS -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include -I{$libunistring_prefix}/include -I{$gettext_prefix}/include"
            LDFLAGS="\$LDFLAGS -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib -L{$libunistring_prefix}/lib/ -L{$gettext_prefix}/lib/ {$ldflags} "
            LIBS="\$LIBS -liconv -lbz2 -lunistring -lintl -lm -pthread "

            CPPFLAGS="\$CPPFLAGS"   \
            LDFLAGS=" -static \$LDFLAGS"  \
            LIBS="\$LIBS"  \
            CFLAGS=" -static "  \
            ./configure \
            --prefix={$libelf_prefix} \
            --enable-install-elfh \
            --with-zlib \
            --with-bzlib \
            --with-lzma \
            --with-zstd \
            --without-biarch \
            --without-valgrind \
            --enable-maintainer-mode \
            --with-libiconv-prefix={$libiconv_prefix} \
            --disable-debuginfod  \
            --disable-libdebuginfod \
            --program-prefix=eu- \
		    --enable-deterministic-archives \
            --with-libintl-prefix={$gettext_prefix}


            make -j {$p->getMaxJob()} config.h
            cd libelf
            make -j {$p->getMaxJob()} libelf.a libelf_pic.a
            cd ..


EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
                mkdir -p {$libelf_prefix}/include/
                mkdir -p {$libelf_prefix}/lib/pkgconfig/
                cp -f config/libelf.pc {$libelf_prefix}/lib/pkgconfig/
                cp -f libelf/*.h {$libelf_prefix}/include/
                cp -f libelf/libelf*.a {$libelf_prefix}/lib/
EOF
            )


        ->withPkgName('libelf')
        ->withDependentLibraries(
            'libarchive',
            'sqlite3',
            'curl',
            'libssh2',
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
            'gettext',
            "zlib",
            'libmicrohttpd',
            'ncurses',
            'libunistring'
        )
    );
};

/*

    undefined reference to libintl_gettext
    https://www.gnu.org/software/gettext/FAQ.html

    nm /usr/local/swoole-cli/gettext/lib/libintl.a |  grep libintl_gettext

 */

/*

     参考
     https://git.alpinelinux.org/aports/tree/main/elfutils/APKBUILD

 */
