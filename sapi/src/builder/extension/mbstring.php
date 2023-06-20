<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('ONIG_CFLAGS', '$(pkg-config --cflags --static oniguruma)');
    $p->withExportVariable('ONIG_LIBS', '$(pkg-config   --libs   --static oniguruma)');
    $p->addExtension(
        (new Extension('mbstring'))
            ->withHomePage('https://www.php.net/mbstring')
            ->withOptions('--enable-mbstring')
            ->withDependentLibraries('oniguruma')
    );
};
