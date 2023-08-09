<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('obs_studio');
    $lib->withHomePage('https://obsproject.com/')
        ->withLicense('https://github.com/obsproject/obs-studio/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('https://github.com/obsproject/obs-studio.git')
        /** 下载依赖库源代码方式一 start **/
        ->withUrl('https://github.com/obsproject/obs-studio/archive/refs/tags/29.1.3.tar.gz')
        ->withFile('obs-studio-29.1.3.tar.gz')
        /** 下载依赖库源代码方式一 end   **/

        /** 使用 autoconfig automake  构建 start  **/
        ->withBuildScript(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
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
        /** 使用 autoconfig automake  构建 end  **/

        ->withBinPath($example_prefix . '/bin/');


    $p->addLibrary($lib);

};
