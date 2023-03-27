<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . BZIP2_PREFIX . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . BZIP2_PREFIX . '/lib');
    $p->withVariable('LIBS', '$LIBS -lbz2');
    $p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=' . BZIP2_PREFIX)->depends('bzip2'));
};
