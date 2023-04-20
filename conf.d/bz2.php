<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $bzip2_prefix = BZIP2_PREFIX;
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $bzip2_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $bzip2_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lbz2');
    $p->addExtension(
        (new Extension('bz2'))
            ->withHomePage('http://php.net/bzip2')
            ->withManual('http://php.net/bzip2')
            ->withOptions('--with-bz2=' . BZIP2_PREFIX)->depends('bzip2')
    );
};
