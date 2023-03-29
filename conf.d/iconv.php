<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . ICONV_PREFIX . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . ICONV_PREFIX . '/lib');
    $p->withVariable('LIBS', '$LIBS -liconv');
    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=' . ICONV_PREFIX)->depends('libiconv'));
};
