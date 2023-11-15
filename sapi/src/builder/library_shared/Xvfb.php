<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('Xvfb');
    $lib->withHomePage('https://www.x.org/archive/X11R7.6/doc/man/man1/Xvfb.1.xhtml')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/opencv/opencv.git')
    ;
    $p->addLibrary($lib);
};
