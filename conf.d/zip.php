<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setExportVarable('LIBZIP_CFLAGS', '$(pkg-config --cflags --static libzip)');
    $p->setExportVarable('LIBZIP_LIBS', '$(pkg-config   --libs   --static libzip)');
    $p->addExtension((new Extension('zip'))->withOptions('--with-zip')->depends('libzip'));
};
