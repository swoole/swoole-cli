<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $liboauth2_prefix = LIBOAUTH2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;
    $cares_prefix = CARES_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('liboauth2');
    $lib->withHomePage('https://github.com/OpenIDC/liboauth2.git')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/OpenIDC/liboauth2.git')
        ->withUrl('https://github.com/OpenIDC/liboauth2/archive/refs/tags/v1.6.3.tar.gz')
        ->withFile('liboauth2-v1.6.3.tar.gz')
        ->withPrefix($example_prefix)


        ->withBuildCached(false)
        ->withInstallCached(false)
        /* 使用 autoconfig automake  构建 start  */
        ->withConfigure(
            <<<EOF
        sh autogen.sh

        ./configure --help



        CURL_CFLAGS=$(pkg-config  --cflags --static libcurl)
        CURL_LIBS=$(pkg-config    --libs   --static libcurl)

        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES zlib"
        # PACKAGES="\$PACKAGES libcurl"
        PACKAGES="\$PACKAGES jansson"
        PACKAGES="\$PACKAGES libpcre2-16 libpcre2-32 libpcre2-8 libpcre2-posix"
        PACKAGES="\$PACKAGES hiredis"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$example_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --with-redis

EOF
        )


        //默认不需要此配置
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$example_prefix}/lib/*.so.*
            rm -rf {$example_prefix}/lib/*.so
            rm -rf {$example_prefix}/lib/*.dylib
EOF
        )

        ->withPkgName('libexample')
        ->withBinPath($example_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries(
            'zlib',
            'openssl',
            'curl',
            'pcre2',
            'hiredis',
            'jansson',
            'cjose'

        )/*

        //默认不需要此配置，特殊目录才需要配置
        ->withLdflags('-L' . $example_prefix . '/lib64')

        //默认不需要此配置，特殊目录才需要配置
        ->withPkgConfig($example_prefix . '/lib/ib64/pkgconfig')

        */
    ;

    $p->addLibrary($lib);


    /*

    //只有当没有 pkgconfig  配置文件才需要编写这里配置; 例子： src/builder/library/bzip2.php

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $example_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $example_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lexample ');

    */

    /*
     //导入需要的变量

    $p->withExportVariable('LIBPQ_CFLAGS', '$(pkg-config  --cflags --static libpq)');
    $p->withExportVariable('LIBPQ_LIBS', '$(pkg-config    --libs   --static libpq)');

     */
};

