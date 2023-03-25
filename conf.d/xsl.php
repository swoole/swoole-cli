<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setExportVarable('XSL_CFLAGS', '$(pkg-config    --cflags --static libxslt)');
    $p->setExportVarable('XSL_LIBS', '$(pkg-config      --libs   --static libxslt)');
    $p->setExportVarable('EXSLT_CFLAGS', '$(pkg-config  --cflags --static libexslt)');
    $p->setExportVarable('EXSLT_LIBS', '$(pkg-config    --libs   --static libexslt)');
    $p->addExtension((new Extension('xsl'))->withOptions('--with-xsl')->depends('libxslt'));
};
