<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $workDir = $p->getWorkDir();

    $options = '--with-xlswriter';
    $options .= ' --enable-reader';

    $options .= ' --with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX;
    $options .= ' --with-expat=' . LIBEXPAT_PREFIX;
    $options .= ' --with-libxlsxio=' . LIBXLSXIO_PREFIX;



    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/include');
    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/library/libxlsxwriter/include');
    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/library/libxlsxwriter/include/xlsxwriter');
    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/library/libxlsxwriter/include/xlsxwriter/third_party');
    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/library/libxlsxio/include');
    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/library/libexpat/expat/lib');
    # $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/');
    #   $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/xlswriter/library/libxlsxwriter/third_party/md5');
    # $p->setVarable('ldflags', '$ldflags -L' . ICONV_PREFIX . '/lib');
    # $p->setVarable('libs', '$libs -liconv');


    $p->addExtension(
        (new Extension('xlswriter'))
            ->withOptions($options)
            ->withPeclVersion('1.5.4')
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->depends('libxlsxwriter', 'libexpat', 'libxlsxio')
    );
};
