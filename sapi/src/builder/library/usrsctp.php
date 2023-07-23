<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $usrsctp_prefix = USRSCTP_PREFIX;
    $lib = new Library('usrsctp');
    $lib->withHomePage('https://github.com/sctplab/usrsctp/')
        ->withLicense('https://github.com/sctplab/usrsctp/blob/master/LICENSE.md', Library::LICENSE_BSD)
        ->withManual('https://github.com/sctplab/usrsctp/blob/master/Manual.md')
        ->withFile('usrsctp-0.9.5.0.tar.gz')
        ->withDownloadScript('usrsctp', <<<EOF
        git clone -b 0.9.5.0 --depth=1 --progress https://github.com/sctplab/usrsctp.git
EOF
    )
        ->withPrefix($usrsctp_prefix)
        ->withConfigure(
            <<<EOF

            sh ./bootstrap
            ./configure --help

            ./configure \
            --prefix={$usrsctp_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-debug=no \
            --enable-inet  \
            --enable-inet6
EOF
        )
        ->withPkgName('opus')
        ->withBinPath($usrsctp_prefix . '/bin/')
    ;

    $p->addLibrary($lib);

};
