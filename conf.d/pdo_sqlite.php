<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('pdo_sqlite'))->withOptions('--with-pdo-sqlite'));
};
