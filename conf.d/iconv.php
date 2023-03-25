<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('cppflags', '$cppflags -I' . ICONV_PREFIX . '/include');
    $p->setVarable('ldflags', '$ldflags -L' . ICONV_PREFIX . '/lib');
    $p->setVarable('libs', '$libs -liconv');
    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=' . ICONV_PREFIX)->depends('libiconv'));

};
