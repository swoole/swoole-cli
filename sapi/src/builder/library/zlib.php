<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('zlib'))
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/madler/zlib/archive/refs/tags/v1.3.1.tar.gz')
            ->withFile('zlib-v1.3.1.tar.gz')
            ->withFileHash('md5', 'ddb17dbbf2178807384e57ba0d81e6a1')
            ->withPrefix(ZLIB_PREFIX)
            ->withConfigure('./configure --prefix=' . ZLIB_PREFIX . ' --static')
            ->withPkgName('zlib')
    );
    $p->withExportVariable('ZLIB_CFLAGS', '$(pkg-config  --cflags --static zlib)');
    $p->withExportVariable('ZLIB_LIBS', '$(pkg-config    --libs   --static zlib)');
};
