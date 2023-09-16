<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('zlib'))
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
            ->withMd5sum('1c9f62f0778697a09d36121ead88e08e')
            ->withPrefix(ZLIB_PREFIX)
            ->withConfigure('./configure --prefix=' . ZLIB_PREFIX . ' --static')
            ->withPkgName('zlib')
    );
    $p->withExportVariable('ZLIB_CFLAGS', '$(pkg-config  --cflags --static zlib)');
    $p->withExportVariable('ZLIB_LIBS', '$(pkg-config    --libs   --static zlib)');
};
