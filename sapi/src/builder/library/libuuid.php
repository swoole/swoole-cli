<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libuuid_prefix = LIBUUID_PREFIX;
    $lib = new Library('libuuid');
    $lib->withHomePage('http://en.wikipedia.org/wiki/Util-linux')
        ->withLicense('https://github.com/util-linux/util-linux/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('http://en.wikipedia.org/wiki/Util-linux')
        ->withManual('http://en.wikipedia.org/wiki/Util-linux/util-linux/tree/v2.39.1/Documentation')
        ->withUrl('https://github.com/util-linux/util-linux/archive/refs/tags/v2.39.3.tar.gz')
        ->withFile('util-linux-v2.39.3.tar.gz')
        ->withPrefix($libuuid_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh
        ./configure --help
        ./configure \
        --prefix={$libuuid_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --disable-all-programs \
        --enable-libuuid \
        --enable-uuidgen \
        --enable-static-programs=uuidd,uuidgen


EOF
        )
        ->withPkgName('uuid')
        ->withBinPath($libuuid_prefix . '/bin/:' . $libuuid_prefix . '/sbin/');

    $p->addLibrary($lib);
};
