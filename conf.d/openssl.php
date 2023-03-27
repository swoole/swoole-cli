<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';
    # openssl v3.0 ； linux 位于 lib64 目录, macOS 位于 lib 目录；
    $openssl_lib = $p->getOsType() === 'linux' ? $openssl_prefix . '/lib64' : $openssl_prefix . '/lib';
    $p->addLibrary(
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withUrl('https://www.openssl.org/source/openssl-3.0.8.tar.gz')
            ->withFile('openssl-3.0.8.tar.gz')
            ->withPrefix($openssl_prefix)
            ->withConfigure(
                <<<EOF
                # ./Configure LIST 
                # INSTALL help info
                ./config {$static} no-shared  --release --prefix={$openssl_prefix}
EOF
            )
            ->withMakeOptions('build_sw')
            ->withMakeInstallCommand('install_sw')
            ->withPkgName('openssl')
            ->withPkgName('libcrypto')
            ->withPkgName('libssl')
            ->withLdflags('-L' . $openssl_lib)
            ->withPkgConfig($openssl_lib . '/pkgconfig')
            ->withBinPath($openssl_prefix . '/bin/')
    );
    $p->addExtension(
        (new Extension('openssl'))
            ->withOptions('--with-openssl --with-openssl-dir=' . OPENSSL_PREFIX)
            ->depends('openssl')
    );
};
