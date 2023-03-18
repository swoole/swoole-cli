<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('ICU_CFLAGS', '$(pkg-config  --cflags --static icu-i18n  icu-io   icu-uc)');
    $p->setVarable('ICU_LIBS', '$(pkg-config    --libs   --static icu-i18n  icu-io   icu-uc)');
    $p->addExtension((new Extension('intl'))->withOptions('--enable-intl')->depends('icu'));
};
