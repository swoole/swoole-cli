
<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmarisa = LIBMARISA_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libmarisa');
    $lib->withHomePage('https://github.com/s-yata/marisa-trie.git')
        ->withLicense('https://github.com/s-yata/marisa-trie/blob/master/COPYING.md', Library::LICENSE_BSD)
        ->withManual('https://github.com/s-yata/marisa-trie.git')

        ->withUrl('https://github.com/s-yata/marisa-trie/archive/refs/tags/v0.2.6.tar.gz')
        ->withFile('libmarisa-v0.2.6.tar.gz')

        ->withPrefix($openssl_prefix)

        //build_type=dev 才生效
        // 自动清理构建目录  用于调试
        ->withCleanBuildDirectory()
        // 自动清理安装目录  用于调试
        ->withCleanPreInstallDirectory($openssl_prefix)
        //明确申明 不使用构建缓存 用于调试
        ->withBuildLibraryCached(false)
        //构建过程中添加代理 （特殊库才需要，比如构建 rav1e 库，构建过程中会自动到代码仓库下载）
        ->withBuildLibraryHttpProxy()

        ->withConfigure(
            <<<EOF
             autoreconf -i
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --enable-native-code \
            --prefix={$openssl_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        /** 使用 autoconfig automake  构建 end  **/

        ->withPkgName('example')
        ->withBinPath($openssl_prefix . '/bin/')

        //依赖其它静态依赖库
        ->withDependentLibraries('zlib', 'openssl', 'depot_tools')

        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$openssl_prefix}/lib/*.so.*
            rm -rf {$openssl_prefix}/lib/*.so
            rm -rf {$openssl_prefix}/lib/*.dylib
EOF
        );

    $p->addLibrary($lib);


    //只有当没有 PKG-CONFIG 配置文件才需要编写这里配置; 例子： src/builder/library/bzip2.php
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $openssl_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $openssl_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lexample ');


};
