<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    // 查看更多 https://git.ffmpeg.org/gitweb

    $ffmpeg_prefix = FFMPEG_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $lib = new Library('ffmpeg');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Library::LICENSE_LGPL
        )
        //->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        //->withFile('ffmpeg-v6.tar.gz')
        ->withManual('https://trac.ffmpeg.org/wiki/CompilationGuide')
        ->withFile('ffmpeg-latest.tar.gz')
        ->withDownloadScript(
            'FFmpeg',
            <<<EOF
            # git clone --depth=1  --single-branch  https://git.ffmpeg.org/ffmpeg.git
            git clone -b master --depth=1  https://github.com/FFmpeg/FFmpeg.git
EOF
        )
        ->withPrefix($ffmpeg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ffmpeg_prefix)
        //->withBuildCached(false)
        ->withConfigure(
            <<<EOF
        # 汇编编译器
        # apk add yasm nasm
        set -x
        ./configure --help
        ./configure --help | grep shared
        ./configure --help | grep static
        ./configure --help | grep  '\-\-extra'
        ./configure --help | grep  'enable'
        # exit 3
        PACKAGES='openssl libwebp  libxml-2.0  freetype2 gmp liblzma' # libssh2
        PACKAGES="\$PACKAGES SvtAv1Dec SvtAv1Enc "
        PACKAGES="\$PACKAGES aom "
        PACKAGES="\$PACKAGES dav1d "
        PACKAGES="\$PACKAGES lcms2 "
        PACKAGES="\$PACKAGES x264 "
        # PACKAGES="\$PACKAGES x265 numa "

         CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) "
         CPPFLAGS="\$CPPFLAGS -I{$libxml2_prefix}/include/ "
         CPPFLAGS="\$CPPFLAGS -I/usr/include "
         LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
         LDFLAGS="\$LDFLAGS -L/usr/lib "
         LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"

        ./configure  \
        --prefix=$ffmpeg_prefix \
        --enable-gpl \
        --enable-version3 \
        --disable-shared \
        --enable-static \
        --enable-openssl \
        --enable-libwebp \
        --enable-libxml2 \
        --enable-libsvtav1 \
        --enable-libdav1d \
        --enable-libaom \
        --enable-lcms2 \
        --enable-gmp \
        --enable-libx264 \
        --enable-random \
        --enable-libfreetype \
        --enable-ffplay \
        --extra-cflags="--static \${CPPFLAGS} " \
        --extra-ldflags="-static \${LDFLAGS} " \
        --extra-libs="\${LIBS} " \
        --extra-ldexeflags="-Bstatic" \
        --pkg-config-flags="--static" \
        --pkg-config=pkg-config \
        --cc={$p->get_C_COMPILER()} \
        --cxx={$p->get_CXX_COMPILER()} \


        # --ld={$p->getLinker()} \
        # --enable-libx265 \
        # --enable-nonfree \
        # --enable-libssh \
        # --enable-cross-compile \


EOF
        )
        ->withPkgName('libavcodec')
        ->withPkgName('libavdevice')
        ->withPkgName('libavfilter')
        ->withPkgName('libavformat')
        ->withPkgName('libavutil')
        ->withPkgName('libswresample')
        ->withPkgName('libswscale')
        ->withBinPath($ffmpeg_prefix . '/bin/')
        ->withDependentLibraries(
            'openssl',
            'zlib',
            'liblzma',
            'libxml2',
            'libwebp',
            'svt_av1',
            'dav1d',
            'aom',
            'freetype',
            "gmp",
            "lcms2",
            "libx264",
            "liblzma"
        ) //  "libx265", 'libssh2',
    ;

    $p->addLibrary($lib);
};
