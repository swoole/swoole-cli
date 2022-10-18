<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('mysqlnd'))->withOptions('--enable-mysqlnd'));
};
