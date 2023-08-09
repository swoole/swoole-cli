<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('zimg');
    $lib->withHomePage('https://github.com/sekrit-twc/zimg.git')
        ->withLicense('https://github.com/sekrit-twc/zimg/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withManual('https://github.com/sekrit-twc/zimg.git')

        ->withAutoUpdateFile() # 明确申明 每次都拉取代码，不使用 pool/lib/opencv-latest.tar.g 文件作为缓存
        ->withFile('zimg-latest.tar.gz')
        ->withDownloadScript(
            'zimg',
            <<<EOF

                git clone -b master --depth=1  --recursive https://github.com/sekrit-twc/zimg.git
EOF
        )
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()  //build_type=dev 才生效  自动清理构建目录  用于调试
        ->withCleanPreInstallDirectory($example_prefix)  //build_type=dev 才生效  自动清理安装目录 用于调试
        ->withBuildLibraryCached(false) //明确申明 不使用构建缓存
        ->withBuildLibraryHttpProxy() //构建过程中添加代理 （特殊库才需要，比如构建 rav1e 库，构建过程中会自动到代码仓库下载）


        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )

        ->withPkgName('zimg')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl') //依赖其它静态依赖库
        ->withLdflags('-L' . $example_prefix . '/lib/x86_64-linux-gnu/') //默认下不需要配，特殊目录才需要配置
        ->withPkgConfig($example_prefix . '/lib/x86_64-linux-gnu/pkgconfig')//默认下不需要配，特殊目录才需要配置
        ->withSkipDownload()
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);


    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $example_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $example_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lopencv ');
};
