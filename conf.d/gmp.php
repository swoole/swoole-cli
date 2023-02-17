<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('gmp'))
            ->withUrl('https://gmplib.org/download/gmp/gmp-6.2.1.tar.lz')
            ->withPrefix(GMP_PREFIX)
            ->withConfigure('./configure --prefix=' . GMP_PREFIX . ' --enable-static --disable-shared')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withPkgName('gmp')
    );
    $p->addExtension((new Extension('gmp'))->withOptions('--with-gmp='. GMP_PREFIX)->depends('gmp'));
};
