<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    # example 1
    $libyuv_prefix = $p->getGlobalPrefix() . '/libyuv';
    $p->addLibrary(
        (new Library('libyuv'))
            ->withUrl('https://chromium.googlesource.com/libyuv/libyuv')
            ->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
            ->withLicense('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
            ->withManual('https://chromium.googlesource.com/libyuv/libyuv/+/HEAD/docs/getting_started.md')
            ->withDownloadScript(
                'libyuv',
                <<<EOF
            git clone -b main --depth=1 https://chromium.googlesource.com/libyuv/libyuv
EOF
            )
            ->withPrefix($libyuv_prefix)
            //->withCleanBuildDirectory()
            //->withCleanPreInstallDirectory($libyuv_prefix)
            ->withBuildScript(
                <<<EOF
                mkdir -p  out
                cd out
                cmake -DCMAKE_INSTALL_PREFIX="{$libyuv_prefix}" \
                -DBUILD_STATIC_LIBS=ON \
                -DBUILD_SHARED_LIBS=OFF  \
                -DCMAKE_BUILD_TYPE="Release" ..
                cmake --build . --config Release
                cmake --build . --target install --config Release

EOF
            )
            ->withPkgName('')
            ->withBinPath($libyuv_prefix . '/bin/')
    );

    # example 2
    $p->addExtension(
        (new Extension('swow'))
            ->withOptions('--enable-swow  --enable-swow-ssl --enable-swow-curl ')
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
            ->withFile('swow-v1.2.0.tar.gz')
            //->withPeclVersion('1.2.0')
            ->withDownloadScript(
                "swow",
                <<<EOF
                git clone -b v1.2.0 https://github.com/swow/swow.git
                mv swow swow-t 
                mv swow-t/ext  swow 
                rm -rf swow-t
EOF
            )
    );
};
