<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $util_linux_prefix = UTIL_LINUX_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    $lib = new Library('util_linux');
    $lib->withHomePage('http://en.wikipedia.org/wiki/Util-linux')
        ->withLicense('https://github.com/util-linux/util-linux/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('http://en.wikipedia.org/wiki/Util-linux')
        ->withManual('http://en.wikipedia.org/wiki/Util-linux/util-linux/tree/v2.39.1/Documentation')
        ->withUrl('https://github.com/util-linux/util-linux/archive/refs/tags/v2.39.3.tar.gz')
        ->withFile('util-linux-v2.39.3.tar.gz')
        ->withPrefix($util_linux_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh
        ./configure --help
        ./configure \
        --prefix={$util_linux_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --disable-all-programs \
        --enable-libuuid \
        --enable-uuidgen \
        --enable-static-programs=uuidd,uuidgen \
        --with-libiconv-prefix={$libiconv_prefix} \
        --with-libintl-prefix={$gettext_prefix} \
        --without-python \
        --without-econf \
        --without-systemd \
        --without-user \
        --disable-login \
        --disable-blkid \
        --disable-fsck \
        --disable-libblkid \
        --disable-libmount \
        --disable-fdisks \
        --disable-libsmartcols \
        --disable-libfdisk

EOF
        )
        ->withPkgName('uuid')
        ->withBinPath([$util_linux_prefix . '/bin', $util_linux_prefix . '/sbin',])
        ->withDependentLibraries('libiconv', 'gettext');

    $p->addLibrary($lib);
};
