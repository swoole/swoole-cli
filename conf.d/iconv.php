<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withPrefix('/usr/libiconv')
            ->withPkgConfig('')
            ->withConfigure('./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
    );
    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=/usr/libiconv'));
};
