<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmicrohttpd_prefix = LIBMICROHTTPD_PREFIX;
    $lib = new Library('libmicrohttpd');
    $lib->withHomePage('https://www.gnu.org/software/libmicrohttpd/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://ftp.gnu.org/gnu/libmicrohttpd/libmicrohttpd-0.9.77.tar.gz')
        ->withManual('https://www.gnu.org/software/libmicrohttpd/')
        ->withPrefix($libmicrohttpd_prefix)
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
