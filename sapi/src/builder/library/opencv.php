<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;
    $ffmpeg_prefix = FFMPEG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $jpeg_prefix = JPEG_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $png_prefix = PNG_PREFIX;
    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $lib = new Library('opencv');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('https://github.com/opencv/opencv/blob/4.x/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withFile('opencv-v5.x.tar.gz')
        ->withDownloadScript(
            'opencv',
            <<<EOF
        git clone -b 5.x --depth 1 --progress  https://github.com/opencv/opencv.git
        cd opencv
        git clone -b 5.x --depth 1 --progress  https://github.com/opencv/opencv_contrib.git
        cd ..
EOF
        )
        ->withPrefix($opencv_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($opencv_prefix)
        ->withBuildScript(
            <<<EOF
        apk add ccache python3-dev
        pip3 install numpy -i https://pypi.tuna.tsinghua.edu.cn/simple
        mkdir -p build
        cd  build

        cmake -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$opencv_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DOPENCV_EXTRA_MODULES_PATH="../opencv_contrib/modules" \
        -DCMAKE_BUILD_TYPE=Release \
        -DWITH_FFMPEG=ON \
        -DFFMPEG_ROOT={$ffmpeg_prefix} \
        -DZLIB_ROOT={$zlib_prefix} \
        -DJPEG_ROOT={$jpeg_prefix} \
        -DTIFF_ROOT={$libtiff_prefix} \
        -DPNG_ROOT={$png_prefix} \
        -DOPENCV_GENERATE_PKGCONFIG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_PERF_TESTS=OFF \
        -DBUILD_EXAMPLES=ON \
        -DBUILD_opencv_apps=OFF \
        -DBUILD_SHARED_LIBS=OFF \
        ..

        ninja
        ninja install
EOF
        )
        ->withPkgName('opencv')
        ->withDependentLibraries(
            'ffmpeg',
            'zlib',
            'libjpeg',
            'libwebp',
            'libpng',
            'freetype',
            'libtiff',
            'vtk'
        ) // openjpeg openEXR HDR
    ;

    $p->addLibrary($lib);
};
