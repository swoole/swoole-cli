<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('LIBZIP_CFLAGS', '$(pkg-config --cflags --static libzip)');
    $p->withExportVariable('LIBZIP_LIBS', '$(pkg-config   --libs   --static libzip)');
    $p->addExtension(
        (new Extension('zip'))
            ->withHomePage('https://www.php.net/zip')
            ->withOptions('--with-zip')
            ->withDependentLibraries('libzip')
    );
};
