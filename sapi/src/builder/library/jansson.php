<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $jansson_prefix = JANSSON_PREFIX;
    $p->addLibrary(
        (new Library('jansson'))
            ->withHomePage('http://www.digip.org/jansson/')
            ->withUrl('https://github.com/akheron/jansson/archive/refs/tags/v2.14.tar.gz')
            ->withFile('jansson-v2.14.tar.gz')
            ->withManual('https://github.com/akheron/jansson.git')
            ->withLicense('https://github.com/akheron/jansson/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withPrefix($jansson_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($jansson_prefix)
            ->withConfigure(
                <<<EOF
             autoreconf -fi
            ./configure --help
            ./configure \
            --prefix={$jansson_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
            ->withPkgName('jansson')
    );
};
