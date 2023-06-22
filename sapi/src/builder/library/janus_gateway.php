<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $janus_gateway_prefix = JANUS_GATEWAY_PREFIX;
    $lib = new Library('janus_gateway');
    $lib->withHomePage('https://janus.conf.meetecho.com/')
        ->withLicense('https://github.com/meetecho/janus-gateway/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/meetecho/janus-gateway/archive/refs/tags/v1.1.4.tar.gz')
        ->withManual('https://janus.conf.meetecho.com/')
        ->withFile('janus-gateway-v1.1.4.tar.gz')
        ->withDownloadScript(
            '',
            <<<EOF
        git clone -b v1.1.4 --depth=1  https://github.com/meetecho/janus-gateway.git
EOF
        )

        ->withPrefix($janus_gateway_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --prefix={$janus_gateway_prefix} \
             --enable-websockets --enable-postprocessing \
             --enable-docs --enable-rest \
             --enable-data-channels
EOF
        )
        ;

    $p->addLibrary($lib);
};
