<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('gmp'))->withOptions('--with-gmp=' . GMP_PREFIX)->depends('gmp'));
};
