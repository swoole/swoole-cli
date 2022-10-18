<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('swoole_loader'))
        ->withOptions('--disable-data_encrypt')
    );
};
