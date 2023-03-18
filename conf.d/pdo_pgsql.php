<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('LIBPQ_CFLAGS', '$(pkg-config --cflags --static libpq)');
    $p->setVarable('LIBPQ_LIBS', '$(pkg-config   --libs   --static libpq)');
    $p->addExtension((new Extension('pdo_pgsql'))->withOptions('--with-pdo-pgsql=' . PGSQL_PREFIX)->depends('pgsql'));
};
