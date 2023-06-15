<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    if (0) {
        $libiconv_prefix = ICONV_PREFIX;
        $libidn2_prefix = LIBIDN2_PREFIX;
        $libunistring_prefix = LIBUNISTRING_PREFIX;
        $p->addLibrary(
            (new Library('libidn2'))
                ->withHomePage('https://gitlab.com/libidn/libidn2')
                ->withManual('https://www.gnu.org/software/libidn/libidn2/manual/')
                ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
                ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
                ->withPrefix($libidn2_prefix)
                ->withConfigure(
                    <<<EOF
            ./configure --help
            ./configure --prefix={$libidn2_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --disable-doc \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libunistring-prefix={$libunistring_prefix} \
            --without-libintl-prefix

EOF
                )
                ->withPkgName('libidn2')
                ->withDependentLibraries('libiconv', 'libunistring')
        );
    }
};
