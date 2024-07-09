<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $cjose_prefix = CJOSE_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('cjose');
    $lib->withHomePage('https://github.com/OpenIDC/cjose.git')
        ->withLicense('cjose ', Library::LICENSE_MIT)
        ->withManual('https://github.com/OpenIDC/cjose.git')
        ->withUrl('https://github.com/OpenIDC/cjose/archive/refs/tags/v0.6.2.3.tar.gz')
        ->withFile('cjose-v0.6.2.3.tar.gz')


        ->withPrefix($cjose_prefix)
        ->withBuildCached(false)

        ->withConfigure(
            <<<EOF
        mkdir build
        cd build
        ../configure --help

        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES jansson"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ../configure \
        --prefix={$cjose_prefix} \
        --enable-shared=no \
        --enable-static=yes  \
        --with-openssl \
        --with-jansson \
        --disable-shared \
        --disable-doxygen-doc
EOF
        )

        ->withPkgName('libexample')
        ->withBinPath($cjose_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries(
            'openssl',
            'jansson'
        )
    ;

    $p->addLibrary($lib);

    /*
     //导入需要的变量

    $p->withExportVariable('LIBPQ_CFLAGS', '$(pkg-config  --cflags --static libpq)');
    $p->withExportVariable('LIBPQ_LIBS', '$(pkg-config    --libs   --static libpq)');

     */
};

