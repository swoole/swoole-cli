<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libusrsctp_prefix = LIBUSRSCTP_PREFIX;
    $lib = new Library('libusrsctp');
    $lib->withHomePage('https://github.com/Kurento/libusrsctp.git')
        ->withLicense('https://github.com/Kurento/libusrsctp/blob/master/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://github.com/Kurento/libusrsctp.git')
        ->withManual('https://github.com/Kurento/libusrsctp.git')
        ->withFile('libusrsctp-latest.tar.gz')
        ->withDownloadScript(
            'libusrsctp',
            <<<EOF
                git clone  --depth=1 https://github.com/Kurento/libusrsctp.git
EOF
        )

        ->withPrefix($libusrsctp_prefix)
        ->withConfigure(
            <<<EOF
        ./bootstrap
        ./configure
EOF
        );

    $p->addLibrary($lib);
};
