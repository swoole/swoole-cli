<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $sdl2_prefix = SDL2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $libunwind_prefix = LIBUNWIND_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $alsa_audio_prefix = ALSA_AUDIO_PREFIX;
    $sndio_audio_prefix = SNDIO_AUDIO_PREFIX;

    $lib = new Library('sdl2');
    $lib->withHomePage('https://libsdl.org')
        ->withLicense('https://github.com/libsdl-org/SDL/blob/main/LICENSE.txt', Library::LICENSE_SPEC)
        ->withManual('https://github.com/libsdl-org/SDL.git')
        ->withFile('SDL-release-2.30.3.tar.gz')
        ->withDownloadScript(
            'SDL',
            <<<EOF
                git clone -b release-2.30.3  --depth=1 https://github.com/libsdl-org/SDL.git
EOF
        )
        ->withPrefix($sdl2_prefix)
        ->withAutoUpdateFile()
        /*
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$libiconv_prefix}/include " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$libiconv_prefix}/lib" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -liconv " \
            ./configure \
            --prefix={$sdl2_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        */
        ->withBuildScript(<<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$sdl2_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON  \
        -DSDL_TEST=OFF  \
        -DSDL_LIBICONV=ON \
        -DLIBICONV_INCLUDE_DIR={$libiconv_prefix}/include \
        -DLIBICONV_LIBRARY={$libiconv_prefix}/lib \
        -DSDL_PTHREADS_SEM=ON \
        -DSDL_ALSA=ON \
        -DSDL_ALSA_SHARED=OFF \
        -DSDL_JACK_SHARED=OFF \
        -DSDL_PIPEWIRE_SHARED=OFF \
        -DSDL_PULSEAUDIO_SHARED=OFF \
        -DSDL_SNDIO_SHARED=OFF \
        -DSDL_X11=OFF \
        -DSDL_X11_SHARED=OFF \
        -DCMAKE_DISABLE_FIND_PACKAGE_wayland-client=ON \
        -DCMAKE_DISABLE_FIND_PACKAGE_wayland-egl=ON \
        -DCMAKE_DISABLE_FIND_PACKAGE_wayland-cursor=ON \
        -DCMAKE_DISABLE_FIND_PACKAGE_egl=ON \
        -DCMAKE_DISABLE_FIND_PACKAGE_egl=libdrm \
        -DCMAKE_DISABLE_FIND_PACKAGE_egl=gbm \
        -DCMAKE_DISABLE_FIND_PACKAGE_egl=egl \
        -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$alsa_audio_prefix};{$libunwind_prefix}"

EOF
        )
        ->withPkgName('sdl2')
        ->withBinPath($sdl2_prefix . '/bin/')
        ->withDependentLibraries(
            'alsa_audio',
            'openssl',
            'libiconv',
            'zlib'
        )
    ;

    $p->addLibrary($lib);
};
