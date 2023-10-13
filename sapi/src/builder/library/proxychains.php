<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $proxychains_prefix = PROXYCHAINS_PREFIX;
    $lib = new Library('proxychains');
    $lib        ->withHomePage('https://github.com/rofl0r/proxychains-ng.git')
        ->withManual('https://github.com/rofl0r/proxychains-ng.git')
        ->withLicense('https://github.com/rofl0r/proxychains-ng/blob/master/COPYING', Library::LICENSE_GPL)
        ->withFile('proxychains-latest.tar.gz')
        ->withDownloadScript(
            'proxychains-ng',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/rofl0r/proxychains-ng.git
EOF
        )

        ->withPrefix($proxychains_prefix)
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF
            ./configure --help

            LDFLAGS=" -static" \
            ./configure \
            --prefix={$proxychains_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )


        ->withPkgName('example')
        ->withBinPath($proxychains_prefix . '/bin/')

        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')


        /*

        //默认不需要此配置
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$proxychains_prefix}/lib/*.so.*
            rm -rf {$proxychains_prefix}/lib/*.so
            rm -rf {$proxychains_prefix}/lib/*.dylib
EOF
        )
        */



        /*

        //默认不需要此配置，特殊目录才需要配置
        ->withLdflags('-L' . $proxychains_prefix . '/lib/x86_64-linux-gnu/')
        //默认不需要此配置，特殊目录才需要配置
        ->withPkgConfig($proxychains_prefix . '/lib/x86_64-linux-gnu/pkgconfig')

        */
    ;

    $p->addLibrary($lib);


    /*

    //只有当没有 pkgconfig  配置文件才需要编写这里配置; 例子： src/builder/library/bzip2.php

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $proxychains_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $proxychains_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lexample ');

    */
};
