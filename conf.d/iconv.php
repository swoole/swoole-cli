<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=' . ICONV_PREFIX)->depends('libiconv'));
};
