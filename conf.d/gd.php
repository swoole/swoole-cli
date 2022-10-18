<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg=/usr --with-freetype=/usr')
    );
};
