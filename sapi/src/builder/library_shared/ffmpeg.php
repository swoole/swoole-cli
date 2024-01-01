<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    // 查看更多 https://git.ffmpeg.org/gitweb

    //更多静态库参考： https://github.com/BtbN/FFmpeg-Builds/tree/master/scripts.d

    //https://github.com/zshnb/ffmpeg-gpu-compile-guide.git

    $ffmpeg_prefix = FFMPEG_PREFIX;


    $cflags = $p->getOsType() == 'macos' ? ' ' : '';
    $libs = $p->getOsType() == 'macos' ? ' -lc++ ' : ' -lstdc++ ';

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
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            # 汇编编译器
            apk add yasm nasm

EOF
        )
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y liblcms2-dev liblcms2-2 liblcms2-utils
        apt install -y libfdk-aac-dev
        apt install -y libvpx-dev
        apt install -y librabbitmq-dev
        apt install -y libopenh264-dev
        apt install -y libopus-dev
        apt install -y libsdl2-dev
        apt install -y libx264-dev
        apt install -y libx265-dev
        apt install -y libwebp-dev libwebpdemux2 libwebpmux3  libyuv-dev
        apt install -y libgmp-dev
        apt install -y liblzma-dev
        apt install -y libdav1d-dev libaom-dev libogg-dev
        apt install -y libfribidi-dev
        apt install -y libfreetype-dev


EOF
        )
        ->withConfigure(
            <<<EOF
            set -x
            #  libavresample 已弃用，默认编译时不再构建它
            # /usr/lib/x86_64-linux-gnu/pkgconfig

            PACKAGES='openssl  libxml-2.0  freetype2 gmp liblzma' # libssh2
            PACKAGES="\$PACKAGES libwebp "
            # PACKAGES="\$PACKAGES SvtAv1Dec SvtAv1Enc "
            PACKAGES="\$PACKAGES aom "
            PACKAGES="\$PACKAGES dav1d "
            PACKAGES="\$PACKAGES lcms2 "
            PACKAGES="\$PACKAGES x264 "
            PACKAGES="\$PACKAGES x265 " # numa
            PACKAGES="\$PACKAGES sdl2 "
            PACKAGES="\$PACKAGES ogg "
            PACKAGES="\$PACKAGES opus "
            PACKAGES="\$PACKAGES openh264 "
            PACKAGES="\$PACKAGES vpx "
            PACKAGES="\$PACKAGES fdk-aac "
            PACKAGES="\$PACKAGES fribidi "
            PACKAGES="\$PACKAGES librabbitmq "

            CPPFLAGS="$(pkg-config  --cflags-only-I   \$PACKAGES) "
            LDFLAGS="$(pkg-config   --libs-only-L     \$PACKAGES) "
            LIBS="$(pkg-config      --libs-only-l     \$PACKAGES) "

            CPPFLAGS="\$CPPFLAGS   "

            LDFLAGS="\$LDFLAGS   "

            LIBS="\$LIBS  {$libs} "


            ./configure  \
            --prefix=$ffmpeg_prefix \
            --enable-gpl \
            --enable-version3 \
            --enable-nonfree \
            --enable-openssl \
            --enable-libwebp \
            --enable-libxml2 \
            --enable-libaom \
            --enable-gmp \
            --enable-libx264 \
            --enable-libx265 \
            --enable-random \
            --enable-libfreetype \
            --enable-libvpx \
            --enable-ffplay \
            --enable-sdl2 \
            --enable-libdav1d \
            --enable-libopus \
            --enable-libopenh264 \
            --enable-libfdk-aac \
            --enable-libfribidi \
            --enable-librabbitmq \
            --enable-lcms2 \
            --enable-libsvtav1 \
            --enable-shared \
            --disable-static \
            --cc={$p->get_C_COMPILER()} \
            --cxx={$p->get_CXX_COMPILER()} \
            --extra-cflags="\${CPPFLAGS} " \
            --extra-ldflags="\${LDFLAGS} " \
            --extra-libs="\${LIBS} " \

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
            'svt_av1'
            //'speex' //被opus 取代
        ) //   'libssh2',
    ;

    $p->addLibrary($lib);
};

/*
 * 基于FFmpeg+VAAPI的硬件加速渲染技术
 * https://zhuanlan.zhihu.com/p/533442023
 * VDPAU和VAAPI加速规范
 */
