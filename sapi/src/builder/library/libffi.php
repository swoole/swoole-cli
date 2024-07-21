<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libffi_prefix = LIBFFI_PREFIX;

    $lib = new Library('libffi');
    $lib->withHomePage('http://sourceware.org/libffi')
        ->withLicense('https://github.com/libffi/libffi/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/libffi/libffi.git')
        ->withUrl('https://github.com/libffi/libffi/archive/refs/tags/v3.4.6.tar.gz')
        ->withFile('libffi-v3.4.6.tar.gz')
        ->withPrefix($libffi_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh

        ./configure --help

        ./configure \
        --prefix={$libffi_prefix} \
        --enable-shared=no \
        --enable-static=yes

EOF
        )
        ->withPkgName('libffi');

    $p->addLibrary($lib);
};
