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
            ->withPrefix(ZLIB_PREFIX)
            ->withConfigure('./configure --prefix=' . ZLIB_PREFIX . ' --static')
            ->withPkgName('zlib')
            ->withDependentLibraries('libxml2', 'bzip2')
    );
};
