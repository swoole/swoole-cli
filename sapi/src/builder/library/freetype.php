<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freetype_prefix = FREETYPE_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $brotli_prefix = BROTLI_PREFIX;
    $p->addLibrary(
        (new Library('freetype'))
            ->withHomePage('https://freetype.org/')
            ->withManual('https://freetype.org/freetype2/docs/documentation.html')
            ->withLicense(
                'https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/GPLv2.TXT',
                Library::LICENSE_GPL
            )
            ->withUrl('https://github.com/freetype/freetype/archive/refs/tags/VER-2-13-2.tar.gz')
            ->withFile('freetype-2.13.2.tar.gz')
            ->withMd5sum('dcd1af080e43fe0c984c34bf3e7d5e16')
            ->withFileHash('md5', 'dcd1af080e43fe0c984c34bf3e7d5e16')
            ->withPrefix($freetype_prefix)
            ->withBuildCached(false)
            ->withBuildScript(
                <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$freetype_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DCMAKE_POLICY_VERSION_MINIMUM=3.5 \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DFT_REQUIRE_ZLIB=TRUE \
            -DFT_REQUIRE_BZIP2=TRUE \
            -DFT_REQUIRE_BROTLI=TRUE \
            -DFT_REQUIRE_PNG=TRUE \
            -DFT_DISABLE_HARFBUZZ=TRUE \
            -DCMAKE_PREFIX_PATH="{$zlib_prefix};{$bzip2_prefix};{$libpng_prefix};{$brotli_prefix}"

            cmake --build . --target install
EOF
            )
            ->withPkgName('freetype2')
            ->withBinPath($freetype_prefix . '/bin/')
            ->withDependentLibraries('zlib', 'bzip2', 'libpng', 'brotli')
    );
};
