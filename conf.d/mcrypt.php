<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $libmcrypt_prefix = LIBMCRYPT_PREFIX;
    $lib = new Library('libmcrypt');
    $lib->withHomePage('https://sourceforge.net/projects/mcrypt/files/Libmcrypt/')
        ->withLicense('https://gitlab.com/libtiff/libtiff/-/blob/master/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/winlibs/libmcrypt/archive/refs/tags/libmcrypt-2.5.8-3.4.tar.gz')
        ->withPrefix($libmcrypt_prefix)
        ->withConfigure(
            <<<EOF
sh ./configure --help
chmod a+x ./install-sh
sh ./configure --prefix=$libmcrypt_prefix \
--enable-static=yes \
--enable-shared=no


EOF
        )
        ->withPkgName('libmcrypt');
    $p->addLibrary($lib);
    $p->addExtension(
        (new Extension('mcrypt'))
            ->withOptions('--with-mcrypt=' . LIBMCRYPT_PREFIX)
            ->withPeclVersion('1.0.5')
            ->withHomePage('https://github.com/php/pecl-encryption-mcrypt')
            ->withLicense('https://github.com/php/pecl-encryption-mcrypt/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->depends('libmcrypt')
    );
};
