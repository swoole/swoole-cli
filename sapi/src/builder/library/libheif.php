<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $libheif_prefix = LIBHEIF_PREFIX;
    $aom_prefix = AOM_PREFIX;
    $libx265_prefix = LIBX265_PREFIX;
    $dav1d_prefix = DAV1D_PREFIX;
    $svt_av1_prefix = SVT_AV1_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libde265_prefix = LIBDE265_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $libopenh264_prefix = OPENH264_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $openjpeg_prefix = OPENJPEG_PREFIX;

    $cmake_prefix_path = "";
    $cmake_prefix_path .= "{$aom_prefix};";
    $cmake_prefix_path .= "{$dav1d_prefix};";
    $cmake_prefix_path .= "{$svt_av1_prefix};";
    $cmake_prefix_path .= "{$libx265_prefix};";
    $cmake_prefix_path .= "{$libjpeg_prefix};";
    $cmake_prefix_path .= "{$libde265_prefix};";
    $cmake_prefix_path .= "{$zlib_prefix};";
    $cmake_prefix_path .= "{$libpng_prefix};";
    $cmake_prefix_path .= "{$libwebp_prefix};";
    $cmake_prefix_path .= "{$libopenh264_prefix};";
    $cmake_prefix_path .= "{$libtiff_prefix};";
    $cmake_prefix_path .= "{$openjpeg_prefix};";


    $lib = new Library('libheif');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('https://github.com/strukturag/libheif#License-1-ov-file', Library::LICENSE_LGPL)
        ->withManual('https://github.com/strukturag/libheif.git')
        ->withUrl('https://github.com/strukturag/libheif/releases/download/v1.20.2/libheif-1.20.2.tar.gz')
        ->withPrefix($libheif_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libheif_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$cmake_prefix_path}" \
        -DWITH_LIBDE265=ON \
        -DWITH_X265=ON \
        -DWITH_JPEG_ENCODER=ON \
        -DWITH_JPEG_DECODER=ON \
        -DWITH_EXAMPLES=OFF \
        -DWITH_GDK_PIXBUF=OFF \
        -DCMAKE_DISABLE_FIND_PACKAGE_Doxygen=ON \
        -DBUILD_TESTING=OFF \
        -DWITH_DAV1D=ON \
        -DWITH_AOM_ENCODER=ON \
        -DWITH_AOM_DECODER=ON \
        -DWITH_SvtEnc=ON \
        -DWITH_OpenH264_ENCODER=ON \
        -DWITH_OpenH264_DECODER=ON \
        -DWITH_OpenH264_DECODER_PLUGIN=OFF \
        -DWITH_DAV1D_PLUGIN=OFF \
        -DWITH_SvtEnc_PLUGIN=OFF \
        -DWITH_OpenJPEG_DECODER=ON \
        -DWITH_OpenJPEG_DECODER_PLUGIN=OFF \
        -DWITH_OpenJPEG_ENCODER=ON \
        -DWITH_OpenJPEG_ENCODER_PLUGIN=OFF \
        -DENABLE_PLUGIN_LOADING=OFF


        cmake --build . --config Release

        cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('libheif')
        ->withBinPath($libheif_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries(
            'libx265',
            'libde265',
            'aom',
            'dav1d',
            'svt_av1',
            'libjpeg',
            'zlib',
            'libpng',
            'libwebp',
            'openh264',
            'libtiff',
            'openjpeg'
        );
    $p->addLibrary($lib);

};
