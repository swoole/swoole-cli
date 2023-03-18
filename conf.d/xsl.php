<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('XSL_CFLAGS', '$(pkg-config    --cflags --static libxslt)');
    $p->setVarable('XSL_LIBS', '$(pkg-config      --libs   --static libxslt)');
    $p->setVarable('EXSLT_CFLAGS', '$(pkg-config  --cflags --static libexslt)');
    $p->setVarable('EXSLT_LIBS', '$(pkg-config    --libs   --static libexslt)');

    $p->addExtension((new Extension('xsl'))->withOptions('--with-xsl')->depends('libxslt'));
};
