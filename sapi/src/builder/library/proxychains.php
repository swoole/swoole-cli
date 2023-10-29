<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $proxychains_prefix = PROXYCHAINS_PREFIX;
    $lib = new Library('proxychains');
    $lib        ->withHomePage('https://github.com/rofl0r/proxychains-ng.git')
        ->withManual('https://github.com/rofl0r/proxychains-ng.git')
        ->withLicense('https://github.com/rofl0r/proxychains-ng/blob/master/COPYING', Library::LICENSE_GPL)
        ->withFile('proxychains-latest.tar.gz')
        ->withDownloadScript(
            'proxychains-ng',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/rofl0r/proxychains-ng.git
EOF
        )

        ->withPrefix($proxychains_prefix)
        ->withBuildCached(false)
        ->withInstallCached(false)
        ->withConfigure(
            <<<EOF
            ./configure --help

            CFLAGS=" -std=gnu11 " \
            LDFLAGS=" -static -std=gnu11 " \
            ./configure \
            --prefix={$proxychains_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withBinPath($proxychains_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);

};
