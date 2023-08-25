<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsctp_prefix = LIBSCTP_PREFIX;

    // SCTP（Stream Control Transmission Protocol，流控制传输协议

    $lib = new Library('libsctp');
    $lib->withHomePage('https://github.com/sctp/lksctp-tools')
        ->withLicense('https://github.com/sctp/lksctp-tools/blob/master/COPYING.lib', Library::LICENSE_LGPL)
        ->withManual('https://github.com/sctp/lksctp-tools.git')
        ->withManual('https://github.com/sctp/lksctp-tools/blob/master/INSTALL')
        ->withFile('lksctp-tools-latest.tar.gz')
        ->withDownloadScript(
            'lksctp-tools',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/sctp/lksctp-tools.git
EOF
        )
        ->withPrefix($libsctp_prefix)
        ->withConfigure(
            <<<EOF
           ./bootstrap

            ./configure --help

            ./configure \
            --prefix={$libsctp_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('libsctp')
        ->withBinPath($libsctp_prefix . '/bin/');
    $p->addLibrary($lib);
};
