<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $oniguruma_prefix = ONIGURUMA_PREFIX;
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withHomePage('https://github.com/kkos/oniguruma.git')
            ->withUrl('https://github.com/kkos/oniguruma/archive/refs/tags/v6.9.9.tar.gz')
            ->withFile('oniguruma-v6.9.9.tar.gz')
            ->withPrefix($oniguruma_prefix)
            ->withConfigure(
                './autogen.sh && ./configure --prefix=' . $oniguruma_prefix . ' --enable-static --disable-shared'
            )
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withPkgName('oniguruma')
            ->withBinPath($oniguruma_prefix . '/bin/')
    );
};
