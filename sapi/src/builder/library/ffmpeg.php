<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    // 查看更多 https://git.ffmpeg.org/gitweb

    $ffmpeg_prefix = FFMPEG_PREFIX;
    $lib = new Library('ffmpeg');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Library::LICENSE_LGPL
        )
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-v6.tar.gz')
        ->withManual('https://trac.ffmpeg.org/wiki/CompilationGuide')
        ->withDownloadScript(
            'FFmpeg',
            <<<EOF
            # git clone --depth=1  --single-branch  https://git.ffmpeg.org/ffmpeg.git
            git clone --depth=1  --single-branch  https://github.com/FFmpeg/FFmpeg.git
EOF
        )
        ->withPrefix($ffmpeg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ffmpeg_prefix)
        ->withConfigure(
            <<<EOF
        # 汇编编译器
        # apk add yasm nasm
        set -x
        ./configure --help
        ./configure --help | grep shared
        ./configure --help | grep static
        ./configure --help | grep  '\-\-extra'
        # exit 3
        PACKAGES='openssl libwebp  libxml-2.0 libssh2 freetype2 gmp liblzma'
        PACKAGES="\$PACKAGES SvtAv1Dec SvtAv1Enc "
        PACKAGES="\$PACKAGES aom "
        PACKAGES="\$PACKAGES dav1d "
        PACKAGES="\$PACKAGES lcms2 "
        PACKAGES="\$PACKAGES x264 "
        PACKAGES="\$PACKAGES x265 "

         CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)"
         LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)"
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
        --enable-libx265 \
        --enable-random \
        --enable-libfreetype \
        --enable-libssh \
        --enable-nonfree \
        --cc={$p->get_C_COMPILER()} \
        --cxx={$p->get_CXX_COMPILER()} \
        --ld={$p->getLinker()} \
        --extra-ldflags="-static \${LDFLAGS} " \
        --extra-libs="\${LIBS} " \
        --extra-cflags=" -static "
        #--pkg-config="\${PKG_CONFIG_PATH}" \
        #--pkg-config-flags="--static" \
EOF
        )
        ->withPkgName('libavcodec  libavdevice  libavfilter  libavformat libavutil  libswresample  libswscale')
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
            'libssh2',
            "gmp",
            "lcms2",
            "libx264",
            "libx265",
            "liblzma"
        )
    ;

    $p->addLibrary($lib);
};
