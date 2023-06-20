<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('LIBSODIUM_CFLAGS', '$(pkg-config --cflags --static libsodium)');
    $p->withExportVariable('LIBSODIUM_LIBS', '$(pkg-config   --libs   --static libsodium)');
    $p->addExtension(
        (new Extension('sodium'))
            ->withHomePage('https://github.com/jedisct1/libsodium-php')
            ->withOptions('--with-sodium')
            ->withDependentLibraries('libsodium')
    );
};
