<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $v4l_utils_prefix = V4L_UTILS_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('v4l_utils');
    $lib->withHomePage('https://git.linuxtv.org/v4l-utils.git')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://git.linuxtv.org/v4l-utils.git/tree/INSTALL.md')
        /* 下载依赖库源代码方式二 start */
        ->withFile('v4l-utils-1.26.1.tar.gz')
        ->withDownloadScript(
            'v4l-utils',
            <<<EOF
            git clone -b v4l-utils-1.26.1  --depth=1 git://linuxtv.org/v4l-utils.git
EOF
        )
        ->withHttpProxy(false, true)
        ->withPrefix($v4l_utils_prefix)
        ->withBuildScript(
            <<<EOF
        meson  -h
        meson setup -h
        # meson configure -h

        meson setup  build \
        -Dprefix={$v4l_utils_prefix} \
        -Dlibdir={$v4l_utils_prefix}/lib \
        -Dincludedir={$v4l_utils_prefix}/include \
        -Dbackend=ninja \
        -Dbuildtype=release \
        -Ddefault_library=static \
        -Db_staticpic=true \
        -Db_pie=true \
        -Dprefer_static=true

        ninja -C build
        DESTDIR={$v4l_utils_prefix} ninja -C build install

EOF
        )
        ->withPkgName('libexample')
        ->withBinPath($v4l_utils_prefix . '/bin/');

    $p->addLibrary($lib);

};

