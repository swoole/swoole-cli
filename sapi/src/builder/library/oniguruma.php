<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $oniguruma_prefix = ONIGURUMA_PREFIX;
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withHomePage('https://github.com/kkos/oniguruma.git')
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/kkos/oniguruma/archive/refs/tags/v6.9.9.tar.gz')
            ->withFile('oniguruma-v6.9.9.tar.gz')
            ->withFileHash('md5', '6a3defb3d5e57c2fa4b6f3b4ec6de28b')
            ->withPrefix($oniguruma_prefix)
            ->withConfigure(
                './autogen.sh && ./configure --prefix=' . $oniguruma_prefix . ' --enable-static --disable-shared'
            )
            ->withPkgName('oniguruma')
            ->withBinPath($oniguruma_prefix . '/bin/')
    );
};
