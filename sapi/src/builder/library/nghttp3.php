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
            ->withUrl('https://github.com/ngtcp2/nghttp3/releases/download/v1.15.0/nghttp3-1.15.0.tar.gz')
            ->withFile('nghttp3-1.15.0.tar.gz')
            ->withFileHash('sha256', '0e431c81eb2d3df5ced048d6e942925ff922ad053e76a0274eea7b164c9b776e')
            ->withPrefix($nghttp3_prefix)
            ->withConfigure(
                <<<EOF
            autoreconf -fi
            ./configure --help
            ./configure \
            --prefix={$nghttp3_prefix} \
            --enable-lib-only \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
            ->withPkgName('libnghttp3')
    );
};
