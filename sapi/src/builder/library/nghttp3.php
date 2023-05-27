<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $nghttp3_prefix = NGHTTP3_PREFIX;
    $p->addLibrary(
        (new Library('nghttp3'))
            ->withHomePage('https://github.com/ngtcp2/nghttp3')
            ->withLicense('https://github.com/ngtcp2/nghttp3/blob/main/COPYING', Library::LICENSE_MIT)
            ->withManual('https://nghttp2.org/nghttp3/')
            ->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/tags/v0.9.0.tar.gz')
            ->withFile('nghttp3-v0.9.0.tar.gz')
            ->withPrefix($nghttp3_prefix)
            ->withConfigure(
                <<<EOF
            autoreconf -fi
            ./configure --help
            ./configure --prefix={$nghttp3_prefix} \
            --enable-lib-only \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
            ->withPkgName('libnghttp3')
    );
};
