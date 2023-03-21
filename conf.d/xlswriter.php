<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $options = '--with-xlswriter';
    $options .= ' --enable-reader';
    $options .= ' --with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX;
    $options .= ' --with-libxlsxio=' . LIBXLSXIO_PREFIX;
    $options .= ' --with-expat=' . LIBEXPAT_PREFIX;

    # --with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withOptions($options)
            ->withPeclVersion('1.5.2')
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->depends('libxlsxwriter', 'libexpat', 'libxlsxio')
    );
};
