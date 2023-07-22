<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $flac_prefix = FLAC_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $lib = new Library('flac');
    $lib->withHomePage('https://xiph.org/flac/')
        ->withLicense('https://github.com/mpruett/audiofile/blob/master/COPYING.GPL', Library::LICENSE_LGPL)
        ->withManual('https://xiph.org/flac/documentation_tasks.html')
        ->withManual('https://github.com/ietf-wg-cellar/flac-specification/wiki/Implementations')
        ->withManual('https://github.com/xiph/flac')
        ->withUrl('https://github.com/xiph/flac/archive/refs/tags/1.4.3.tar.gz')
        ->withFile('libflac-1.4.3.tar.gz')

        ->withPrefix($flac_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            sh ./autogen.sh
            ./configure --help
            LIBS=' -lstdc++ -lm ' \
            ./configure \
            --prefix={$flac_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-libiconv-prefix={$libiconv_prefix}

EOF
        )
        ->withPkgName('flac')
        ->withPkgName('flac++')
        ->withBinPath($flac_prefix . '/bin/')
    ;


    $p->addLibrary($lib);
};
