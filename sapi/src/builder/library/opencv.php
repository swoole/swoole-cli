<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;

    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;

    $openssl_prefix = OPENSSL_PREFIX;
    $ffmpeg_prefix = FFMPEG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
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
    $blas_prefix = BLAS_PREFIX;
    $lapack_prefix = LAPACK_PREFIX;
    $harfbuzz_prefix = HARFBUZZ_PREFIX;
    $glog_prefix = GLOG_PREFIX;
    $imath_prefix = IMATH_PREFIX;
    $libeigen_prefix = LIBEIGEN_PREFIX;
    $vtk_prefix = VTK_PREFIX;
    $fftw3_prefix = FFTW3_PREFIX;
    $libdc1394_prefix = LIBDC1394_PREFIX;
    $glog_prefix = GLOG_PREFIX;
    $libeigen_prefix = LIBEIGEN_PREFIX;
    $suitesparse_prefix = SUITESPARSE_PREFIX;
    $harfbuzz_prefix = HARFBUZZ_PREFIX;


    $CMAKE_PREFIX_PATH = "{$openssl_prefix};";
    $CMAKE_PREFIX_PATH .= "{$zlib_prefix};";

    $CMAKE_PREFIX_PATH .= "{$libxml2_prefix};";
    $CMAKE_PREFIX_PATH .= "{$jpeg_prefix};";
    $CMAKE_PREFIX_PATH .= "{$png_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libwebp_prefix};";
    $CMAKE_PREFIX_PATH .= "{$freetype_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libtiff_prefix};";

    $CMAKE_PREFIX_PATH .= "{$gmp_prefix};";

    $CMAKE_PREFIX_PATH .= "{$liblzma_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libzstd_prefix};";
    $CMAKE_PREFIX_PATH .= "{$liblz4_prefix};";

    $CMAKE_PREFIX_PATH .= "{$gflags_prefix};";
    $CMAKE_PREFIX_PATH .= "{$openblas_prefix};";
    $CMAKE_PREFIX_PATH .= "{$blas_prefix};";
    $CMAKE_PREFIX_PATH .= "{$lapack_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libeigen_prefix};";
    $CMAKE_PREFIX_PATH .= "{$glog_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$vtk_prefix};";
    $CMAKE_PREFIX_PATH .= "{$ffmpeg_prefix};";
    $CMAKE_PREFIX_PATH .= "{$imath_prefix};";
    $CMAKE_PREFIX_PATH .= "{$fftw3_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libdc1394_prefix};";
    $CMAKE_PREFIX_PATH .= "{$glog_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libeigen_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$suitesparse_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$harfbuzz_prefix};";


    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $lib = new Library('opencv');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('https://github.com/opencv/opencv/blob/4.x/LICENSE', Library::LICENSE_APACHE2)
        //->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withManual('https://docs.opencv.org/5.x/db/d05/tutorial_config_reference.html')
        ->withManual('https://github.com/opencv/opencv_contrib/tree/5.x/modules/README.md')
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
        apk add ccache python3-dev binaryen doxygen

        pip3 install numpy setuptools utils-misc  gapi  utils

        # apk add binaryen # WebAssembly 的优化器和编译器/工具链
EOF
        )
        ->withPrefix($opencv_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($opencv_prefix)
        ->withBuildLibraryHttpProxy(true)
        //->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES  zlib"
        PACKAGES="\$PACKAGES  libxml-2.0"
        PACKAGES="\$PACKAGES  liblzma  libzstd liblz4 "
        PACKAGES="\$PACKAGES  libavcodec libavdevice libavfilter libavformat libavutil libswresample libswscale"
        PACKAGES="\$PACKAGES  libwebp libpng libpng16 libjpeg libturbojpeg SvtAv1Enc SvtAv1Dec "

        PACKAGES="\$PACKAGES  dav1d sdl2 aom freetype2  gmp lcms2 " # libtiff-4
        PACKAGES="\$PACKAGES  libbrotlicommon libbrotlidec libbrotlienc"
        PACKAGES="\$PACKAGES  x264 vpx ogg opus openh264 libpcap fdk-aac fribidi librabbitmq x265 gflags "
        PACKAGES="\$PACKAGES  fftw3q  Imath  libglog openblas blas64 lapack64 blas"
        # PACKAGES="\$PACKAGES  harfbuzz-icu harfbuzz-subse harfbuzz"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)"
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"

        CPPFLAGS="\$CPPFLAGS -I{$bzip2_prefix}/include -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include " \
        LDFLAGS="\$LDFLAGS -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib" \
        LIBS="\$LIBS -lbz2 -liconv " \


        mkdir -p build
        cd  build

        cmake .. \
        -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$opencv_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DOPENCV_EXTRA_MODULES_PATH="../opencv_contrib/modules" \
        -DCMAKE_CXX_STANDARD=14 \
        -DCMAKE_C_STANDARD=11 \
        -DCMAKE_BUILD_TYPE=Release \
        -DBUILD_STATIC_LIBS=ON \
        -DBUILD_SHARED_LIBS=OFF \
        -DOpenCV_STATIC=ON \
        -DENABLE_PIC=ON \
        -DWITH_FFMPEG=ON \
        -DFFMPEG_ROOT={$ffmpeg_prefix} \
        -DZLIB_ROOT={$zlib_prefix} \
        -Dfreetype2_ROOT={$freetype_prefix} \
        -DPNG_ROOT={$png_prefix} \
        -DJPEG_ROOT={$jpeg_prefix} \
        -DLAPACK_ROOT={$lapack_prefix} \
        -DOpenBLAS_ROOT={$openblas_prefix} \
        -DOPENCV_GENERATE_PKGCONFIG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_PERF_TESTS=OFF \
        -DBUILD_EXAMPLES=ON \
        -DBUILD_opencv_apps=ON \
        -DCMAKE_PREFIX_PATH='{$CMAKE_PREFIX_PATH}' \
        -DBUILD_opencv_js=OFF \
        -DBUILD_JAVA=OFF \
        -DBUILD_CUDA_STUBS=OFF  \
        -DBUILD_FAT_JAVA_LIB=OFF  \
        -DBUILD_ANDROID_SERVICE=OFF \
        -DBUILD_OBJC=OFF \
        -DBUILD_KOTLIN_EXTENSIONS=OFF \
        -DINSTALL_C_EXAMPLES=ON \
        -DINSTALL_PYTHON_EXAMPLES=ON \
        -DBUILD_DOCS=ON \
        -DOPENCV_ENABLE_NONFREE=ON \
        -DWITH_AVIF=ON \
        -DWITH_GTK=OFF \
        -DWITH_CUDA=OFF \


        # -DOPENCV_EXTRA_MODULE_LINKER_FLAG="-Wl,--no-dynamic-linker -Wl,-Bstatic \${LIBS}"

        # -DCMAKE_MODULE_LINKER_FLAGS="-Wl,--no-dynamic-linker -Wl,-Bstatic \${LIBS}"


        # OPENCV_LINKER_DEFENSES_FLAGS_COMMON
        # OPENCV_EXTRA_MODULE_LINKER_FLAG
        # OPENCV_EXTRA_MODULE_LINKER_FLAGS_RELEAS
        # 参考 https://github.com/opencv/opencv_3rdparty/blob/ffmpeg/4.x_20230622/ffmpeg/build_videoio_plugin.sh

        # -DOPENCV_EXTRA_FLAGS="-DCV_EXPORTS= -D_GNU_SOURCE="

         # -DTIFF_ROOT={$libtiff_prefix} \
         # -Dharfbuzz_ROOT={$harfbuzz_prefix} \




        # -DCMAKE_C_STANDARD_LIBRARIES=" -llzma  -lzstd  -llz4" \
        # -DCMAKE_STATIC_LINKER_FLAGS="-Wl,--no-dynamic-linker -Wl,-Bstatic \${LIBS} "


        #  -DENABLE_BUILD_HARDENING=ON \



        # -DINCLUDE_DIRECTORIES


        # -DCMAKE_REQUIRED_LIBRARIES="-L{$liblzma_prefix}/lib/ -L{$libzstd_prefix}/lib/ -L{$liblz4_prefix}/lib/ -llzma  -lzstd  -llz4"


        # -DCMAKE_STATIC_LINKER_FLAGS="{$liblzma_prefix}/lib/liblzma.a {$libzstd_prefix}/lib/libzstd.a {$liblz4_prefix}/lib/liblz4.a"



        # -DCMAKE_STATIC_LINKER_FLAGS="-L{$liblzma_prefix}/lib/ -L{$libzstd_prefix}/lib/ -L{$liblz4_prefix}/lib/ -llzma  -lzstd  -llz4"

        # -DLINK_LIBRARIES="{$liblzma_prefix}/lib/liblzma.a {$libzstd_prefix}/lib/libzstd.a {$liblz4_prefix}/lib/liblz4.a " \
        # -DLINK_DIRECTORIES="{$liblzma_prefix}/lib/:{$libzstd_prefix}/lib/:{$liblz4_prefix}/lib/"

        # -DTARGET_LINK_LIBRARIES="-llzma  -lzstd  -llz4 "
        #  -DCMAKE_TARGET_LINK_LIBRARIES="-llzma  -lzstd  -llz4 "

        # -DLINK_LIBRARIES="lzma  zstd  lz4"
        # -DCMAKE_EXE_LINKER_FLAGS_INIT='\${LIBS}'
        # -DCMAKE_EXE_LINKER_FLAGS="-L{$liblzma_prefix}/lib/ -L{$libzstd_prefix}/lib/ -L{$liblz4_prefix}/lib/ -llzma  -lzstd  -llz4 "
        # -DCMAKE_REQUIRED_LIBRARIES="lzma  zstd  lz "
        # -DCMAKE_C_IMPLICIT_LINK_LIBRARIES='\${LIBS}'  \
        # -DTARGET_LINK_LIBRARIES='\${LIBS}'

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
            //'libtiff',
            "gmp",
            'liblzma',
            'gflags',
            'fftw3', //快速傅立叶变换库
            'openblas', //基础线性代数程序集
            'lapack', //线性代数计算库
            // 'harfbuzz',
            //'openexr',
            //'openjpeg',
            //'vtk',
            //'opencl',
            //'libdc1394'
            'imath',
            'gflags',
            'glog',
            //'libeigen', //线性运算代数库 (依赖待完善）
            //'suitesparse',
            'harfbuzz'
        )   //   HDR
        ->withBinPath($opencv_prefix . '/bin/')
        ->withLdflags(" -L" . $opencv_prefix . '/lib/opencv5/3rdparty/ ')
    ;

    $p->addLibrary($lib);
};

/*
 * https://github.com/opencv/ade.git
 */

/*
 *  Automatically Tuned Linear Algebra Software (ATLAS)
 *  https://math-atlas.sourceforge.net/
 */

/*
 * libmv  运动轨迹重建
 * https://github.com/opencv/opencv_contrib/tree/master/modules/sfm
 */

/*
 * WebNN
 */
