<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = '--enable-opcache';
    $buildType = $p->getInputOption('with-build-type');
    if ($buildType == 'debug') {
        $p->setExportVarable('VALGRIND_CFLAGS', '$(pkg-config  --cflags --static valgrind)');
        $p->setExportVarable('VALGRIND_LIBS', '$(pkg-config    --libs   --static valgrind)');

        $capstone_prefix = CAPSTONE_PREFIX;
        $p->setExportVarable('CAPSTONE_CFLAGS', $capstone_prefix . '/include');
        $p->setExportVarable('CAPSTONE_LIBS', $capstone_prefix . '/lib');


        $options .= ' --enable-memory-sanitizer ';
        $options .= ' --enable-address-sanitizer ';
        $options .= ' --enable-undefined-sanitizer ';
        $options .= ' --enable-debug ';
    }

    if ($p->getInputOption('with-valgrind')) {
        $options .= ' --with-valgrind ';
    }

    if ($p->getInputOption('disable-opcache-jit')) {
        $options .= ' --disable-opcache-jit ';
    }
    if ($p->getInputOption('enable-gcov')) {
        $options .= ' --enable-gcov ';
    }

    $p->addExtension((new Extension('opcache'))->withOptions($options));
};
