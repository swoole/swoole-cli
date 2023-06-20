<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('bz2'))
            ->withHomePage('http://php.net/bzip2')
            ->withManual('http://php.net/bzip2')
            ->withOptions('--with-bz2=' . BZIP2_PREFIX)->withDependentLibraries('bzip2')
    );
};
