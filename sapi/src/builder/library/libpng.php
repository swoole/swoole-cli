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
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withMd5sum('6c7519f6c75939efa0ed3053197abd54')
            ->withPrefix($libpng_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib )" \
                LDFLAGS="$(pkg-config   --libs-only-L    --static zlib )" \
                LIBS="$(pkg-config      --libs-only-l    --static zlib )" \
                ./configure --prefix={$libpng_prefix} \
                --enable-static --disable-shared \
                --with-zlib-prefix={$libzlib_prefix} \
                --with-binconfigs
EOF
            )
            ->withPkgName('libpng')
            ->withPkgName('libpng16')
            ->withBinPath($libpng_prefix . '/bin')
            ->withDependentLibraries('zlib')
    );
};
