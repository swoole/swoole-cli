<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;
    $ffmpeg_prefix = FFMPEG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $jpeg_prefix = JPEG_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $png_prefix = PNG_PREFIX;
    $gmp_prefix = GMP_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $freetype_prefix = FREETYPE_PREFIX;
    $gflags_prefix = GFLAGS_PREFIX;
    $openblas_prefix = OPENBLAS_PREFIX;
    $lapack_prefix = LAPACK_PREFIX;
    $harfbuzz_prefix = HARFBUZZ_PREFIX;
    $glog_prefix = GLOG_PREFIX;
    $imath_prefix = IMATH_PREFIX;

    $CMAKE_PREFIX_PATH = "{$jpeg_prefix};";
    $CMAKE_PREFIX_PATH .= "{$png_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libtiff_prefix};";
    $CMAKE_PREFIX_PATH .= "{$gmp_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libwebp_prefix};";
    $CMAKE_PREFIX_PATH .= "{$liblzma_prefix};";
    $CMAKE_PREFIX_PATH .= "{$freetype_prefix};";
    $CMAKE_PREFIX_PATH .= "{$gflags_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libzstd_prefix};";
    $CMAKE_PREFIX_PATH .= "{$liblz4_prefix};";
    $CMAKE_PREFIX_PATH .= "{$openblas_prefix};";
    $CMAKE_PREFIX_PATH .= "{$lapack_prefix}";


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
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            apt install ccache python3-dev
            apt install -y python3-numpy
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add ccache python3-dev
        pip3 install numpy setuptools utils-misc  gapi mat_wrapper utils
EOF
        )
        ->withBuildLibraryHttpProxy(true)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF

        mkdir -p build
        cd  build

        cmake .. \
        -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$opencv_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DOPENCV_EXTRA_MODULES_PATH="../opencv_contrib/modules" \
        -DCMAKE_BUILD_TYPE=Release \
        -DBUILD_STATIC_LIBS=ON \
        -DBUILD_SHARED_LIBS=OFF \
        -DOpenCV_STATIC=ON \
        -DWITH_FFMPEG=ON \
        -DFFMPEG_ROOT={$ffmpeg_prefix} \
        -DZLIB_ROOT={$zlib_prefix} \
        -Dfreetype2_ROOT={$freetype_prefix} \
        -DPNG_ROOT={$png_prefix} \
        -DTIFF_ROOT={$libtiff_prefix} \
        -DJPEG_ROOT={$jpeg_prefix} \
        -DLAPACK_ROOT={$lapack_prefix} \
        -DOpenBLAS_ROOT={$openblas_prefix} \
        -DOPENCV_GENERATE_PKGCONFIG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_PERF_TESTS=OFF \
        -DBUILD_EXAMPLES=ON \
        -DBUILD_opencv_apps=ON \
        -DCMAKE_PREFIX_PATH="{$CMAKE_PREFIX_PATH}" \
        -DCMAKE_REQUIRED_LIBRARIES="lzma  zstd  lz "


        # -DCMAKE_STATIC_LINKER_FLAGS="{$liblzma_prefix}/lib/liblzma.a {$libzstd_prefix}/lib/libzstd.a {$liblz4_prefix}/lib/liblz4.a"

        # -Dharfbuzz_ROOT={$harfbuzz_prefix} \

        # -DCMAKE_STATIC_LINKER_FLAGS="-L{$liblzma_prefix}/lib/ -L{$libzstd_prefix}/lib/ -L{$liblz4_prefix}/lib/ -llzma  -lzstd  -llz4"

        # -DLINK_LIBRARIES="{$liblzma_prefix}/lib/liblzma.a {$libzstd_prefix}/lib/libzstd.a {$liblz4_prefix}/lib/liblz4.a " \
        # -DLINK_DIRECTORIES="{$liblzma_prefix}/lib/:{$libzstd_prefix}/lib/:{$liblz4_prefix}/lib/"

        # -DTARGET_LINK_LIBRARIES="-llzma  -lzstd  -llz4 "

        # -DLINK_LIBRARIES="lzma  zstd  lz4"
        # -DCMAKE_EXE_LINKER_FLAGS="-L{$liblzma_prefix}/lib/ -L{$libzstd_prefix}/lib/ -L{$liblz4_prefix}/lib/ -llzma  -lzstd  -llz4 "
        # -DCMAKE_REQUIRED_LIBRARIES="lzma  zstd  lz "



        # OpenJPEG


        ninja
        ninja install
EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            LINE_NUMBER=$(grep -n 'Libs.private:' {$opencv_prefix}/lib/pkgconfig/opencv5.pc |cut -d ':' -f 1)
            sed -i.save "\${LINE_NUMBER} s/-lIconv::Iconv//" {$opencv_prefix}/lib/pkgconfig/opencv5.pc
EOF
        )
        ->withPkgName('opencv5')
        ->withDependentLibraries(
            'ffmpeg',
            'zlib',
            'libjpeg',
            'libwebp',
            'libpng',
            'freetype',
            'libtiff',
            "gmp",
            'liblzma',
            'gflags',
            'fftw3', //快速傅立叶变换库
            'openblas', //基础线性代数程序集
            'lapack', //线性代数计算库
            // 'harfbuzz',
           // 'imath',
            //'openexr',
            //'openjpeg'
        )   //   HDR   'vtk'
        ->withBinPath($opencv_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
};

/*
 * https://github.com/opencv/ade.git
 */
