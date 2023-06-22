<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsrtp_prefix = LIBSRTP_PREFIX;
    $lib = new Library('libsrtp');
    $lib->withHomePage('https://github.com/cisco/libsrtp/')
        ->withLicense('https://github.com/cisco/libsrtp/blob/main/LICENSE', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/cisco/libsrtp/archive/refs/tags/v2.5.0.tar.gz')
        ->withManual('https://github.com/cisco/libsrtp/')
        ->withFile('libsrtp-v2.5.0.tar.gz')
        ->withPrefix($libsrtp_prefix)
        ->withBuildScript(
            <<<EOF

EOF
        )
    ;

    $p->addLibrary($lib);
};
