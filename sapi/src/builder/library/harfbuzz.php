<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $harfbuzz_prefix = HARFBUZZ_PREFIX;
    $lib = new Library('harfbuzz');
    $lib->withHomePage('http://harfbuzz.github.io/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/harfbuzz/harfbuzz.git')
        ->withUrl('https://github.com/harfbuzz/harfbuzz/archive/refs/tags/8.1.1.tar.gz')
        ->withFile('harfbuzz-8.1.1.tar.gz')
        ->withPrefix($harfbuzz_prefix)
        ->withConfigure(
            <<<EOF
            ./autogen.sh
            ./configure --help

            PACKAGES='icu-i18n icu-io icu-uc '
            PACKAGES="\$PACKAGES freetype2"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lpthread" \
            ./configure \
            --prefix="{$harfbuzz_prefix}"  \
            --enable-static=yes \
            --enable-shared=no \
            --with-libstdc++=yes \
            --with-icu=yes \
            --with-freetype=yes

EOF
        )
        ->withPkgName('harfbuzz-icu')
        ->withPkgName('harfbuzz')
        ->withBinPath($harfbuzz_prefix . '/bin/')
        ->withDependentLibraries('icu', 'freetype')
    ;


    $p->addLibrary($lib);
};

/*
 * chafa https://github.com/hpjansson/chafa.git    Terminal graphics
 * cairo https://www.cairographics.org/        Cairo 是一个支持多种输出设备的 2D 图形库
 *
 * uniscribe  https://github.com/janlelis/uniscribe.git   emoj
 *            https://character.construction/             emoj 大全
 */
