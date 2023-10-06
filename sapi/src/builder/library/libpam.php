<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libpam_prefix = LIBPAM_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('libpam');
    $lib->withHomePage('https://github.com/linux-pam/linux-pam.git')
        ->withLicense('https://github.com/linux-pam/linux-pam/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/linux-pam/linux-pam.git')
        /* 下载依赖库源代码方式二 start */
        ->withFile('linux-pam-v1.5.3.tar.gz')
        ->withDownloadScript(
            'linux-pam',
            <<<EOF
                git clone -b v1.5.3  --depth=1 https://github.com/linux-pam/linux-pam.git
EOF
        )
        ->withPreInstallCommand('alpine', <<<EOF
apk add gettext-dev

apk add docbook5-xml
apk add docbook-xsl-ns

EOF
    )
        ->withPrefix($libpam_prefix)


         // 当--with-build_type=dev 时 如下2个配置才生效

        // 自动清理构建目录
        ->withCleanBuildDirectory()

        // 自动清理安装目录
        ->withCleanPreInstallDirectory($libpam_prefix)


        //明确申明 不使用构建缓存 例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildLibraryCached(false)

        ->withConfigure(
            <<<EOF
        sh ./autogen.sh


        ./configure --help

        # LDFLAGS="\$LDFLAGS -static"

        PACKAGES='openssl  ncursesw '

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$libpam_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-openssl \
        --with-libiconv-prefix={$libiconv_prefix} \
        --disable-prelude \
        --disable-audit \
        --enable-db=no \
        --disable-nis \
        --disable-selinux \
        --disable-econf \
        --disable-nls \
        --disable-rpath \
        --disable-pie \
        --disable-doc


        # --with-libintl-prefix=
EOF
        )
        ->withPkgName('pam')
        ->withPkgName('pam_misc')
        ->withPkgName('pamc')
        ->withBinPath($libpam_prefix . '/bin/')

        //依赖其它静态链接库
        ->withDependentLibraries('openssl', 'ncurses')

    ;

    $p->addLibrary($lib);
};
