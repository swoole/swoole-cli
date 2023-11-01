<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('zlib'))
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withUrl('https://zlib.net/zlib-1.3.tar.gz')
            ->withPrefix(ZLIB_PREFIX)
            ->withConfigure('./configure --prefix=' . ZLIB_PREFIX . ' --static')
            ->withPkgName('zlib')
    );
    $p->withExportVariable('ZLIB_CFLAGS', '$(pkg-config  --cflags --static zlib)');
    $p->withExportVariable('ZLIB_LIBS', '$(pkg-config    --libs   --static zlib)');
};
