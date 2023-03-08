<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    # --with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withOptions('--enable-reader --with-xlswriter '  )
            ->withPeclVersion('1.5.2')
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->depends('libxlsxwriter')
    );
};
