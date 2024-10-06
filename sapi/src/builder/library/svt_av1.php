<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $svt_av1_prefix = SVT_AV1_PREFIX;
    $lib = new Library('svt_av1');
    $lib->withHomePage('https://gitlab.com/AOMediaCodec/SVT-AV1.git')
        ->withLicense(
            'https://gitlab.com/AOMediaCodec/SVT-AV1/-/blob/master/LICENSE.md',
            Library::LICENSE_BSD
        )
        ->withDownloadScript(
            'SVT-AV1',
            <<<EOF
            git clone -b v1.7.0 --depth=1 https://gitlab.com/AOMediaCodec/SVT-AV1.git
EOF
        )
        ->withFile('SVT-AV1-v1.7.0.tar.gz')
        ->withManual('https://gitlab.com/AOMediaCodec/SVT-AV1.git')
        ->withManual('https://gitlab.com/AOMediaCodec/SVT-AV1/-/blob/master/Docs/Build-Guide.md')
        ->withPrefix($svt_av1_prefix)
        ->withBuildScript(
            <<<EOF
            cd Build
            cmake .. \
            -G"Unix Makefiles" \
            -DCMAKE_INSTALL_PREFIX={$svt_av1_prefix} \
            -DCMAKE_INSTALL_LIBDIR={$svt_av1_prefix}/lib \
            -DCMAKE_INSTALL_INCLUDEDIR={$svt_av1_prefix}/include \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON
            make -j {$p->getMaxJob()}
            make install
EOF
        )
        ->withPkgName('SvtAv1Enc')
        ->withPkgName('SvtAv1Dec')
        ->withBinPath($svt_av1_prefix . '/bin/');

    $p->addLibrary($lib);
};
