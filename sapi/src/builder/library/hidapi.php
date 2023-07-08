<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $hidapi_prefix = HIDAPI_PREFIX;
    $lib = new Library('hidapi');
    $lib->withHomePage('https://github.com/signal11/hidapi.git')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_GPL)
        ->withUrl('https://github.com/signal11/hidapi/archive/refs/tags/hidapi-0.7.0.tar.gz')
        ->withManual('https://github.com/signal11/hidapi.git')
        ->withPrefix($hidapi_prefix)
        ->withConfigure(
            <<<EOF
            # ./bootstrap
            ./configure \
            --prefix={$hidapi_prefix} \

EOF
        )
        ->withDependentLibraries('libiconv')
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
