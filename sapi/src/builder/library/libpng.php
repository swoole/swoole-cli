<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libpng_prefix = PNG_PREFIX;
    $libzlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('libpng'))
            ->withHomePage('http://www.libpng.org/pub/png/libpng.html')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withUrl('https://sourceforge.net/projects/libpng/files/libpng16/1.6.43/libpng-1.6.43.tar.gz')
            ->withMd5sum('cee1c227d1f23c3a2a72341854b5a83f')
            ->withPrefix($libpng_prefix)
            /*
            ->withConfigure(
                <<<EOF
                ./configure --help
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib )" \
                LDFLAGS="$(pkg-config   --libs-only-L    --static zlib )" \
                LIBS="$(pkg-config      --libs-only-l    --static zlib )" \
                ./configure \
                --prefix={$libpng_prefix} \
                --enable-static=yes \
                --enable-shared=no \
                --with-zlib-prefix={$libzlib_prefix} \
                --with-binconfigs
EOF
            )
            */
            ->withBuildScript(
                <<<EOF
                mkdir -p build
                cd build
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$libpng_prefix} \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON \
                -DPNG_SHARED=OFF  \
                -DPNG_STATIC=ON  \
                -DPNG_TESTS=OFF \
                -DCMAKE_PREFIX_PATH="{$libzlib_prefix}"

                cmake --build . --config Release --target install

EOF
            )
            ->withPkgName('libpng')
            ->withPkgName('libpng16')
            ->withBinPath($libpng_prefix . '/bin')
            ->withDependentLibraries('zlib')
    );
};
