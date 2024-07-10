<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freetype_prefix = FREETYPE_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('freetype'))
            ->withHomePage('https://freetype.org/')
            ->withManual('https://freetype.org/freetype2/docs/documentation.html')
            ->withLicense(
                'https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/GPLv2.TXT',
                Library::LICENSE_GPL
            )
            ->withUrl('https://sourceforge.net/projects/freetype/files/freetype2/2.13.2/freetype-2.13.2.tar.gz')
            ->withMd5sum('d99447cf00c5f2679918f66f2b5771f6')
            ->withFileHash('md5', 'd99447cf00c5f2679918f66f2b5771f6')
            ->withPrefix($freetype_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            PACKAGES='zlib libpng  libbrotlicommon  libbrotlienc libbrotlidec '
            BZIP2_CFLAGS="-I{$bzip2_prefix}/include"  \
            BZIP2_LIBS="-L{$bzip2_prefix}/lib -lbz2"  \
            CPPFLAGS="$(pkg-config --cflags-only-I --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config  --libs-only-L   --static \$PACKAGES)" \
            LIBS="$(pkg-config     --libs-only-l   --static \$PACKAGES)" \
            CFLAGS="-std=gnu11" \
            ./configure \
            --prefix={$freetype_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-zlib=yes \
            --with-bzip2=yes \
            --with-png=yes \
            --with-harfbuzz=no  \
            --with-brotli=yes \
            --enable-freetype-config

EOF
            )
            ->withPkgName('freetype2')
            ->withBinPath($freetype_prefix . '/bin/')
            ->withDependentLibraries('zlib', 'bzip2', 'libpng', 'brotli')
    );
};
