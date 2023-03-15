<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withPrefix($libiconv_prefix)
            ->withPkgConfig('')
            ->withConfigure('./configure --prefix=' . $libiconv_prefix . ' enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withBinPath($libiconv_prefix . '/bin/')
    );
    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=' . $libiconv_prefix));
};
