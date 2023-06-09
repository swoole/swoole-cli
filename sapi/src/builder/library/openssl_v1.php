<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_v1_PREFIX;
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';
    $p->addLibrary(
        (new Library('openssl_v1'))
            ->withHomePage('https://www.openssl.org/')
            ->withUrl('https://www.openssl.org/source/openssl-1.1.1t.tar.gz')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withPrefix($openssl_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($openssl_prefix)
            ->withConfigure(
                <<<EOF
                ./config {$static} no-shared --prefix={$openssl_prefix} --libdir={$openssl_prefix}/lib
EOF
            )
            ->withMakeInstallCommand('install_sw')
            ->withPkgName('openssl')
            ->withBinPath($openssl_prefix . '/bin/')
    );
};
