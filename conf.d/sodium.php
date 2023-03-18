<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('LIBSODIUM_CFLAGS', '$(pkg-config --cflags --static libsodium)');
    $p->setVarable('LIBSODIUM_LIBS', '$(pkg-config   --libs   --static libsodium)');
    $p->addExtension((new Extension('sodium'))->withOptions('--with-sodium'));
};
