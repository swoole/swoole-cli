<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $workDir = $p->getWorkDir();

    $options = '--with-xlswriter';
    $options .= ' --enable-reader';
    /*
    $options .= ' --with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX;
    $options .= ' --with-libxlsxio=' . LIBXLSXIO_PREFIX;
    $options .= ' --with-expat=' . LIBEXPAT_PREFIX;
    */
    $options .= ' --with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX;
    $options .= ' --with-libxlsxio=' . $workDir . '/ext/xlswriter/library/libxlsxio/';
    # $options .= ' --with-expat=' . $workDir . '/ext/xlswriter/library/libexpat/expat/lib';
    $options .= ' --with-expat=' . LIBEXPAT_PREFIX;


    $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . $workDir . '/ext/xlswriter/include');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . $workDir . '/ext/xlswriter/');
    # $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . $workDir . '/ext/xlswriter/library/libxlsxwriter/include');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . $workDir . '/ext/xlswriter/library/libxlsxwriter/third_party/md5/');
    # $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . $workDir . '/ext/xlswriter/library/libexpat/expat/lib');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . $workDir . '/ext/xlswriter/library/libxlsxio/include');

    # $p->setExportVarable('SWOOLE_CLI_EXTRA_LDLAGS', '$SWOOLE_CLI_EXTRA_LDLAGS -L' . $workDir . '/ext/xlswriter/library/libexpat/expat/lib');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_LDLAGS', '$SWOOLE_CLI_EXTRA_LDLAGS -L' . $workDir . '/ext/xlswriter/library/libxlsxio/lib');
    # $p->setExportVarable('SWOOLE_CLI_EXTRA_LIBS', '$SWOOLE_CLI_EXTRA_LIBS -liconv');

    $p->addExtension(
        (new Extension('xlswriter'))
            ->withOptions($options)
            ->withPeclVersion('1.5.2')
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->depends('libxlsxwriter','libexpat')
        //->depends('libxlsxwriter', 'libexpat', 'libxlsxio')
    );
};
