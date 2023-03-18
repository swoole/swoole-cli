<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $option = '--enable-opcache';
    $buildType = $p->getInputOption('with-build-type');
    if ($buildType == 'debug') {
        $p->setVarable('VALGRIND_CFLAGS', '$(pkg-config  --cflags --static valgrind)');
        $p->setVarable('VALGRIND_LIBS', '$(pkg-config    --libs   --static valgrind)');
        $capstone_prefix = CAPSTONE_PREFIX;

        $p->setVarable('CAPSTONE_CFLAGS',  $capstone_prefix . '/include');
        $p->setVarable('CAPSTONE_LIBS', $capstone_prefix . '/lib');


    } else {
    }
    if ($p->getOsType() == 'macos') {
        $option .= ' --disable-opcache-jit';
    }

    $p->addExtension((new Extension('opcache'))->withOptions($option));
};
