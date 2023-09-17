<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';

    $p->addLibrary(
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withManual('https://www.openssl.org/docs/')
            ->withUrl('https://www.openssl.org/source/openssl-1.1.1w.tar.gz')
            ->withPrefix($openssl_prefix)
            ->withConfigure(
                <<<EOF
                # ./Configure LIST

                # php 8 之前的版本不支持 openssl v3    详情 https://github.com/swoole/swoole-cli/issues/84

                ./config {$static} no-shared  enable-tls1_3 --release \
                --prefix={$openssl_prefix} \
                --libdir={$openssl_prefix}/lib

EOF
            )
            ->withMakeInstallCommand('install_sw')
            ->withPkgName('libcrypto')
            ->withPkgName('libssl')
            ->withPkgName('openssl')
            ->withBinPath($openssl_prefix . '/bin/')
    );
};
