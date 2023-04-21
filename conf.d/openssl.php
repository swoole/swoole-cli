<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';
    $p->addLibrary(
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withManual('https://www.openssl.org/docs/')
            ->withUrl('https://github.com/quictls/openssl/archive/refs/tags/openssl-3.0.8-quic1.tar.gz')
            ->withPrefix($openssl_prefix)
            ->withConfigure(
                <<<EOF
                 # ./Configure LIST
                ./config {$static} no-shared  enable-tls1_3 --release --prefix={$openssl_prefix} --libdir=${openssl_prefix}/lib

EOF
            )
            ->withMakeOptions('build_sw')
            ->withMakeInstallCommand('install_sw')
            ->withPkgName('libcrypto')
            ->withPkgName('libssl')
            ->withPkgName('openssl')
            ->withPkgName('libcrypto')
            ->withPkgName('libssl')
            ->withBinPath($openssl_prefix . '/bin/')
    );

    $p->addExtension(
        (new Extension('openssl'))
            ->withHomePage('https://www.php.net/openssl')
            ->withOptions('--with-openssl --with-openssl-dir=' . OPENSSL_PREFIX)
            ->depends('openssl')
    );
};
