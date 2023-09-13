<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    //FFTW ( the Faster Fourier Transform in the West) 是一个快速计算离散傅里叶变换的标准C语言程序集
    //快速傅立叶变换库
    $fftw3_prefix = FFTW3_PREFIX;
    $lib = new Library('fftw3');
    $lib->withHomePage('http://www.fftw.org/')
        ->withLicense('https://github.com/FFTW/fftw3/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/BtbN/FFmpeg-Builds/blob/master/scripts.d/25-fftw3.sh')
        ->withManual('https://github.com/FFTW/fftw3/')
        ->withFile('fftw3-latest.tar.gz')
        ->withDownloadScript(
            'fftw3',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/FFTW/fftw3.git
EOF
        )
        ->withPrefix($fftw3_prefix)
        ->withBuildScript(
            <<<EOF
        mkdir -p build
        cd build
        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$fftw3_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DBUILD_TESTS=OFF \
        -DENABLE_OPENM=ON \
        -DENABLE_THREADS=ON \
        -DWITH_COMBINED_THREADS=ON \
        -DENABLE_FLOAT=ON \
        -DENABLE_LONG_DOUBLE=ON \
        -DENABLE_QUAD_PRECISION=ON \
        -DENABLE_SSE=ON \
        -DENABLE_SSE2=ON \
        -DENABLE_AVX=ON \
        -DENABLE_AVX2=ON


        cmake --build . --config Release

        cmake --build . --config Release --target install
EOF
        )
        ->withPkgName('fftw3q')
    ;

    $p->addLibrary($lib);
};
