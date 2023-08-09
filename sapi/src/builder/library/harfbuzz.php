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
        ->withConfigure(
            <<<EOF
            ./autogen.sh
            ./configure --help

            LIBS="lpthread" \
            ./configure \
            --prefix="{$harfbuzz_prefix}"
            --disable-shared
            --enable-static
            --with-pic
EOF
        )
        ->withPkgName('harfbuzz-icu')
        ->withPkgName('harfbuzz-subse')
        ->withPkgName('harfbuzz')
        ->withBinPath($harfbuzz_prefix . '/bin/');


    $p->addLibrary($lib);
};
