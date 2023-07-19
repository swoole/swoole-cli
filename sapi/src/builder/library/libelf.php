<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libelf_prefix = LIBELF_PREFIX;
    $p->addLibrary(
        (new Library('libelf'))
            ->withHomePage('https://github.com/WolfgangSt/libelf.git')
            ->withLicense('https://github.com/WolfgangSt/libelf/blob/master/COPYING.LIB', Library::LICENSE_GPL)
            ->withFile('libelf-latest.tar.gz')
            ->withManual('https://github.com/WolfgangSt/libelf/blob/master/INSTALL')
            ->withDownloadScript('libelf', <<<EOF
                git clone --depth=1 https://github.com/WolfgangSt/libelf.git
EOF
            )
            ->withPrefix($libelf_prefix)
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                # autoconf
                ./configure --help
                ./configure \
                --prefix={$libelf_prefix} \
                --enable-compat \
                --enable-shared=no

EOF
            )
            ->withMakeOptions('all')
            //->withMakeInstallCommand('install-local')
            ->withPkgName('libelf')
    );
};
