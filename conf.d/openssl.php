<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary((new Library('openssl'))
        ->withUrl('https://www.openssl.org/source/openssl-1.1.1p.tar.gz')
        ->withPrefix('/usr/openssl')
        ->withConfigure('./config' . ($p->getOsType() === 'macos' ? '' : ' -static --static') . ' no-shared --prefix=/usr/openssl')
        ->withMakeInstallOptions('install_sw')
        ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
        ->withHomePage('https://www.openssl.org/')
    );
    $p->addExtension(
        (new Extension('openssl'))
            ->withOptions('--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl')
            ->depends('openssl')
    );
};
