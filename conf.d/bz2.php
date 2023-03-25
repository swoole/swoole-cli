<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libbzip2_prefix = BZIP2_PREFIX;
    $p->setVarable('cppflags', '$cppflags -I' . $libbzip2_prefix . '/include');
    $p->setVarable('ldflags', '$ldflags -L' . $libbzip2_prefix . '/lib');
    $p->setVarable('libs', '$libs -lbz2');
    $p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=' . BZIP2_PREFIX)->depends('bzip2'));
};
