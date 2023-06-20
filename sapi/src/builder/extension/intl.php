<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('ICU_CFLAGS', '$(pkg-config  --cflags --static icu-i18n  icu-io   icu-uc)');
    $p->withExportVariable('ICU_LIBS', '$(pkg-config    --libs   --static icu-i18n  icu-io   icu-uc)');
    $p->addExtension(
        (new Extension('intl'))
            ->withHomePage('https://www.php.net/intl')
            ->withOptions('--enable-intl')
            ->withDependentLibraries('icu')
    );
};
