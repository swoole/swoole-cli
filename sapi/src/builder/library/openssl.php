<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $static = $p->isMacos() ? '' : ' -static --static';

    $p->addLibrary(
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withManual('https://www.openssl.org/docs/')
            ->withUrl('https://github.com/quictls/openssl/archive/refs/tags/openssl-3.1.4-quic1.tar.gz')
            ->withFileHash('md5', 'ba2d8774a51a38f2481aad43d05aea57')
            ->withPrefix($openssl_prefix)
            ->withConfigure(
                <<<EOF
                # Fix openssl error, "-ldl" should not be added when compiling statically
                sed -i.backup 's/add("-ldl", threads("-pthread"))/add(threads("-pthread"))/g' ./Configurations/10-main.conf
                # ./Configure LIST
               ./config {$static} no-shared  enable-tls1_3 --release \
               --prefix={$openssl_prefix} \
               --libdir={$openssl_prefix}/lib \
               --openssldir=/etc/ssl

EOF
            )
            ->withMakeOptions('build_sw')
            ->withMakeInstallCommand('install_sw')
            ->withScriptAfterInstall(
                <<<EOF
            sed -i.backup "s/-ldl/  /g" {$openssl_prefix}/lib/pkgconfig/libcrypto.pc
EOF
            )
            ->withPkgName('libcrypto')
            ->withPkgName('libssl')
            ->withPkgName('openssl')
            ->withBinPath($openssl_prefix . '/bin/')
    );
};
