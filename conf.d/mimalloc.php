<?php

use SwooleCli\Preprocessor;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('mimalloc'))
            ->withUrl('https://github.com/microsoft/mimalloc/archive/refs/tags/v2.0.7.tar.gz')
            ->withFile('mimalloc-2.0.7.tar.gz')
            ->withPrefix('/usr/mimalloc')
            ->withConfigure("cmake . -DMI_BUILD_SHARED=OFF -DCMAKE_INSTALL_PREFIX=/usr/mimalloc -DMI_INSTALL_TOPLEVEL=ON -DMI_PADDING=OFF -DMI_SKIP_COLLECT_ON_EXIT=ON -DMI_BUILD_TESTS=OFF")
            ->withPkgName('libmimalloc')
            ->withLicense('https://github.com/microsoft/mimalloc/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://microsoft.github.io/mimalloc/')
            ->withLdflags('-L/usr/mimalloc/lib -lmimalloc')
    );
};

