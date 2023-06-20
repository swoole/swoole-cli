<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('XSL_CFLAGS', '$(pkg-config    --cflags --static libxslt)');
    $p->withExportVariable('XSL_LIBS', '$(pkg-config      --libs   --static libxslt)');
    $p->withExportVariable('EXSLT_CFLAGS', '$(pkg-config  --cflags --static libexslt)');
    $p->withExportVariable('EXSLT_LIBS', '$(pkg-config    --libs   --static libexslt)');
    $p->addExtension(
        (new Extension('xsl'))
            ->withHomePage('https://www.php.net/xsl')
            ->withOptions('--with-xsl')
            ->withDependentLibraries('libxslt')
    );
};
