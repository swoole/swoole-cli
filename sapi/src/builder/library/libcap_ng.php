<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    # Linux Capabilities

    $libcap_ng_prefix = LIBCAP_NG_PREFIX;
    $lib = new Library('libcap_ng');
    $lib->withHomePage('libcap-ng')
        ->withLicense('https://github.com/stevegrubb/libcap-ng/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withManual('https://github.com/stevegrubb/libcap-ng.git')
        ->withFile('libcap-ng-latest.tar.gz')
        ->withDownloadScript(
            'libcap-ng',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/stevegrubb/libcap-ng.git
EOF
        )
        ->withPrefix($libcap_ng_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help

            ./configure \
            --prefix={$libcap_ng_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --without-python \
            --without-python3

EOF
        )

        ->withPkgName('libcap-ng')
        ->withBinPath($libcap_ng_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
};
