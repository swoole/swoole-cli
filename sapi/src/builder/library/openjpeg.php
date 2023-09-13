<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $openjpeg_prefix = OPENJPEG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $liblcms2_prefix = LCMS2_PREFIX;
    $libpng_prefix = PNG_PREFIX;

    $libzstd_prefix = LIBZSTD_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;


    //文件名称 和 库名称一致
    $lib = new Library('openjpeg');
    $lib->withHomePage('https://www.openjpeg.org/')
        ->withLicense('https://github.com/uclouvain/openjpeg/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/uclouvain/openjpeg/blob/master/INSTALL.md')
        ->withUrl('https://github.com/uclouvain/openjpeg/archive/refs/tags/v2.5.0.tar.gz')
        ->withFile('openjpeg-v2.5.0.tar.gz')

        # 补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm
            pip3 install meson
EOF
        )
        ->withPrefix($openjpeg_prefix)
        ->withCleanBuildDirectory()
       ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$openjpeg_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_TESTING=OFF \
            -DBUILD_JPIP=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Java=ON \
            -DCMAKE_PREFIX_PATH="{$zlib_prefix};{$libtiff_prefix};{$liblcms2_prefix};{$libpng_prefix}" \
            -DLINK_LIBRARIES="{$liblzma_prefix}/lib/liblzma.a {$libzstd_prefix}/lib/libzstd.a {$liblz4_prefix}/lib/liblz4.a"

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('libopenjp2')
        ->withBinPath($openjpeg_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'libpng', 'libtiff', 'lcms2')

    ;

    $p->addLibrary($lib);
};
