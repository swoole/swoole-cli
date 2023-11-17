<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $qemu_prefix = QEMU_PREFIX;

    $lib = new Library('qemu');
    $lib
        ->withHomePage('http://www.qemu.org/')
        ->withLicense('https://github.com/qemu/qemu/blob/master/COPYING.LIB', Library::LICENSE_GPL)
        ->withManual('https://www.qemu.org/download/')->withBinPath($qemu_prefix . '/bin/')
        ->withFile('qemu-latest.tar.gz')
        ->withDownloadScript(
            'qemu',
            <<<EOF
        git clone -b master https://gitlab.com/qemu-project/qemu.git
        cd qemu
        git submodule init
        git submodule update --recursive

EOF
        )
        ->withPrefix($qemu_prefix)
        ->withConfigure(
            <<<EOF
        ./configure
        make

EOF
        )
        ->withBinPath($qemu_prefix . '/bin/');
    $p->addLibrary($lib);
};
