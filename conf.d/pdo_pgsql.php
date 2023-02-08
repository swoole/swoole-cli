<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('pdo_pgsql'))->withOptions('--with-pdo-pgsql=/usr/pgsql'));
};
