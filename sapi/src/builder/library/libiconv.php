<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libiconv'))
            ->withHomePage('https://www.gnu.org/software/libiconv/')
            ->withManual('https://www.gnu.org/software/libiconv/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withPrefix($libiconv_prefix)
            ->withConfigure('./configure --prefix=' . $libiconv_prefix . ' enable_static=yes enable_shared=no')
            ->withBinPath($libiconv_prefix . '/bin/')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . ICONV_PREFIX . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . ICONV_PREFIX . '/lib');
    $p->withVariable('LIBS', '$LIBS -liconv');
};
