<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('ONIG_CFLAGS', '$(pkg-config --cflags --static oniguruma)');
    $p->setVarable('ONIG_LIBS', '$(pkg-config   --libs   --static oniguruma)');
    $p->addExtension(
        (new Extension('mbstring'))
            ->withOptions('--enable-mbstring')
    );
};
