<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('pgsql'))->withOptions('--with-pgsql=' . PGSQL_PREFIX)->depends('pgsql'));
};
