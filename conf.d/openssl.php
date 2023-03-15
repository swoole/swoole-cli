<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('openssl'))
         ->withHomePage('https://www.openssl.org/')
         ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
        ->withUrl('https://www.openssl.org/source/openssl-1.1.1p.tar.gz')
        ->withPrefix($openssl_prefix)
        ->withConfigure('./config' . ($p->getOsType() === 'macos' ? '' : ' -static --static') . ' no-shared --prefix=' . OPENSSL_PREFIX)
        ->withMakeInstallCommand('install_sw')
        ->withPkgName('openssl')
        ->withBinPath($openssl_prefix . '/bin/')
    );
    $p->addExtension(
        (new Extension('openssl'))
            ->withOptions('--with-openssl --with-openssl-dir=' . OPENSSL_PREFIX)
            ->depends('openssl')
    );
};
