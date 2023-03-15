<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $oniguruma_prefix = ONIGURUMA_PREFIX;
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withUrl('https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7')
            ->withPrefix($oniguruma_prefix)
            ->withConfigure('./autogen.sh && ./configure --prefix=' . $oniguruma_prefix . ' --enable-static --disable-shared')
            ->withFile('oniguruma-6.9.7.tar.gz')
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withPkgName('oniguruma')
            ->withBinPath($oniguruma_prefix . '/bin/')
    );
    $p->addExtension(
        (new Extension('mbstring'))
        ->withOptions('--enable-mbstring')
        ->depends('oniguruma')
    );
};
