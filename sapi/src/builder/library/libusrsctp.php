<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libusrsctp_prefix = LIBUSRSCTP_PREFIX;
    $lib = new Library('libusrsctp');
    $lib->withHomePage('https://github.com/sctplab/usrsctp.git')
        ->withLicense('https://github.com/sctplab/usrsctp/blob/master/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://github.com/sctplab/usrsctp.git')
        ->withManual('https://github.com/sctplab/usrsctp.git')
        ->withFile('libusrsctp-latest.tar.gz')
        ->withAutoUpdateFile()
        ->withDownloadScript(
            'libusrsctp',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/sctplab/usrsctp.git
EOF
        )

        ->withPrefix($libusrsctp_prefix)
        ->withConfigure(
            <<<EOF
        ./bootstrap
        ./configure --help
        ./configure \
        --prefix={$libusrsctp_prefix} \
        --enable-shared=no \
        --enable-static=yes

EOF
        );

    $p->addLibrary($lib);
};
