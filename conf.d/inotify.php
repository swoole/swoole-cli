<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('inotify'))
        ->withOptions('--enable-inotify')
        ->withPeclVersion('3.0.0'));
};
