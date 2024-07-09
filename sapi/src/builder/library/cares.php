<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('cares'))
            ->withHomePage('https://c-ares.org/')
            ->withManual('https://c-ares.org/')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withUrl('https://github.com/c-ares/c-ares/releases/download/cares-1_24_0/c-ares-1.24.0.tar.gz')
            ->withFileHash('md5', '25b872ee1c3bee8ff5f49b5f31307002')
            ->withPrefix(CARES_PREFIX)
            ->withConfigure('./configure --prefix=' . CARES_PREFIX . ' --enable-static --disable-shared --disable-tests')
            ->withPkgName('libcares')
    );
};
