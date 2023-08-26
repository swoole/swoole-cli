<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('pjsua2');
    $lib->withHomePage('https://www.pjsip.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://www.pjsip.org/docs/book-latest/html/intro_pjsua2.html#building-pjsua2')

        /* 下载依赖库源代码方式二 start */
        ->withFile('opencv-latest.tar.gz')
        ->withDownloadScript(
            'opencv',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/opencv/opencv.git
EOF
        )
        /* 下载依赖库源代码方式二 end   */



        ->withPrefix($example_prefix)
        /*
         //用于调试
         //当 --with-build_type=dev 时 如下2个配置生效


        // 自动清理构建目录
        ->withCleanBuildDirectory()

        // 自动清理安装目录
        ->withCleanPreInstallDirectory($example_prefix)


        //明确申明 不使用构建缓存
        //例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildLibraryCached(false)

       */


        # 构建源码可以使用cmake 、 autoconfig 、 meson 构建等



        /* 使用 autoconfig automake  构建 start  */
        ->withConfigure(
            <<<EOF
            # sh autogen.sh

            # libtoolize -ci
            # autoreconf -fi

            ./configure --help

            # LDFLAGS="\$LDFLAGS -static"

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes


            ./configure --enable-shared
            make dep & make
            sudo make install

EOF
        )
        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')

        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);

};
