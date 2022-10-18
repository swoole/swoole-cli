<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=/usr/libiconv'));
};
