<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $lzo_prefix = LZO_PREFIX;
    $lib = new Library('lzo');
    $lib->withHomePage('http://www.oberhumer.com/opensource/lzo/')
        ->withLicense('http://www.oberhumer.com/opensource/gpl.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/eyeseaevan/lzo-2.10/blob/master/INSTALL')
        ->withUrl('http://www.oberhumer.com/opensource/lzo/download/lzo-2.10.tar.gz')
        ->withPrefix($lzo_prefix)
        ->withConfigure(
            <<<EOF

            ./configure --help
            ./configure \
            --prefix={$lzo_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('lzo2');


    $p->addLibrary($lib);

};
