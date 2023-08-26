<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libffi_prefix = LIBFFI_PREFIX;
    $p->addLibrary(
        (new Library('libffi'))
            ->withHomePage('https://sourceware.org/libffi/')
            ->withLicense('http://github.com/libffi/libffi/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/libffi/libffi/releases/download/v3.4.4/libffi-3.4.4.tar.gz')
            ->withFile('libffi-3.4.4.tar.gz')
            ->withPrefix($libffi_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libffi_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help ;
            ./configure \
            --prefix={$libffi_prefix} \
            --enable-shared=no \
            --enable-static=yes
            EOF
            )
            ->withPkgName('libffi')
    );
};
