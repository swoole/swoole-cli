<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('LIBZIP_CFLAGS', '$(pkg-config --cflags --static libzip)');
    $p->withExportVariable('LIBZIP_LIBS', '$(pkg-config   --libs   --static libzip)');


    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $options = '--with-zip';
    if ($php_version_id < 7040) {
        $options = '--enable-zip --with-libzip=' . ZIP_PREFIX;
    }

    $p->addExtension(
        (new Extension('zip'))
            ->withHomePage('https://www.php.net/zip')
            ->withOptions($options)
            ->withDependentLibraries('libzip')
    );
};
