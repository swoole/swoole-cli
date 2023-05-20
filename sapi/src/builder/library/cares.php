<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('cares'))
            ->withHomePage('https://c-ares.org/')
            ->withManual('https://c-ares.org/')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withUrl('https://c-ares.org/download/c-ares-1.19.0.tar.gz')
            ->withPrefix(CARES_PREFIX)
            ->withConfigure('./configure --prefix=' . CARES_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('libcares')
    );
};
