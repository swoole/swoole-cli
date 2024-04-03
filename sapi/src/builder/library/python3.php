<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $python3_prefix = PYTHON3_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('python3');
    $lib->withHomePage('https://www.python.org/')
        ->withLicense('https://docs.python.org/3/license.html', Library::LICENSE_LGPL)
        ->withManual('https://www.python.org')
        ->withUrl('https://www.python.org/ftp/python/3.11.8/Python-3.11.8.tgz')
        ->withPrefix($python3_prefix)
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF

        ./configure --help


        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES zlib"
        PACKAGES="\$PACKAGES sqlite3"
        PACKAGES="\$PACKAGES liblzma"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$bzip2_prefix}/include/ " \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$bzip2_prefix}/lib/  " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lbz2 " \
        ./configure \
        --prefix={$python3_prefix} \
        --enable-shared=no \
        --disable-shared
EOF
        )
        //->withPkgName('example')
        ->withBinPath($python3_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl', 'sqlite3', 'bzip2', 'liblzma');

    $p->addLibrary($lib);

};
