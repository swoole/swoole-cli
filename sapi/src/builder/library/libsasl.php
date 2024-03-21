<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsasl_prefix = LIBSASL_PREFIX;
    $url = 'https://github.com/cyrusimap/cyrus-sasl/releases/download/cyrus-sasl-2.1.28/cyrus-sasl-2.1.28.tar.gz';
    $p->addLibrary(
        (new Library('libsasl'))
            ->withHomePage('https://www.cyrusimap.org/sasl/')
            ->withManual('https://www.cyrusimap.org/sasl/sasl/installation.html#installation')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl($url)
            ->withFile('cyrus-sasl-2.1.28.tar.gz')
            ->withPrefix($libsasl_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                # 支持很多参数，按需要启用
                ./configure \
                --prefix={$libsasl_prefix} \
                 --enable-static=yes \
                 --enable-shared=no \
EOF
            )
            ->withPkgName('libsasl2')
            ->withBinPath($libsasl_prefix . '/sbin/')
    );
};
