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
        ./configure --help
        ./configure  \
        --prefix=$ffmpeg_prefix \
        --enable-openssl \
        --enable-libwebp \
        --enable-libxml2 \
        --enable-libsvtav1 \
        --enable-libdav1d \
        --enable-libaom \
        --enable-libfreetype \
        --enable-libssh \
        --enable-lcms2 \
        --enable-gmp \
        --enable-libx264 \
        -=enable-libx265


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
            "libx265"
        )
    ;

    $p->addLibrary($lib);
};
