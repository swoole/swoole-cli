<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $xorg_util_macros_prefix = XORG_UTIL_MACROS_PREFIX;
    $lib = new Library('xorg_util_macros');
    $lib->withHomePage('https://gitlab.freedesktop.org/xorg/util/macros/')
    ->withLicense('https://gitlab.freedesktop.org/xorg/util/macros/-/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withManual('https://gitlab.freedesktop.org/xorg/util/macros/-/blob/util-macros-1.20.0/INSTALL')
        ->withFile('xorg-util-macros-1.20.0.tar.gz')
        ->withDownloadScript(
            'macros',
            <<<EOF
            git clone -b util-macros-1.20.0 --depth=1  https://gitlab.freedesktop.org/xorg/util/macros.git
EOF
        )
        ->withPrefix($xorg_util_macros_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($xorg_util_macros_prefix)
        ->withConfigure(
            <<<EOF
              ls -lha .
             # ./autogen.sh
             autoreconf -i
             ./configure --help
             ./configure \
             --prefix={$xorg_util_macros_prefix}
EOF
        )
        ->withPkgName('xorg-macros')
        ->withPkgConfig($xorg_util_macros_prefix . '/share/pkgconfig/')
    ;

    $p->addLibrary($lib);

    // xutils 包含了必须的
    // 参考 https://salsa.debian.org/xorg-team/app/xutils-dev
};
