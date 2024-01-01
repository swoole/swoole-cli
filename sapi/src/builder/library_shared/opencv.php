<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;

    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $lib = new Library('opencv');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('https://github.com/opencv/opencv/blob/4.x/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/opencv/opencv.git')
        ->withManual('https://docs.opencv.org/5.x/db/d05/tutorial_config_reference.html')
        ->withManual('https://github.com/opencv/opencv_contrib/tree/5.x/modules/README.md')
        ->withManual('https://github.com/opencv/opencv/blob/5.x/doc/tutorials/introduction/config_reference/config_reference.markdown')
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

        pip3 install numpy setuptools utils-misc  gapi  utils lxml beautifulsoup4 graphviz

        # apk add binaryen # WebAssembly 的优化器和编译器/工具链
EOF
        )
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y libstdc++-12-dev
        apt install -y libavif-dev
        apt install -y libvtk9-dev
        apt install -y libogre-1.12-dev
        apt install -y doxygen
        apt install -y python3-flake8
        apt install -y libgflags-dev
        apt install -y libva-dev
        # apt install -y libgtk-3-dev
        # apt install -y libgtk-4-dev
        apt install -y libdc1394-25
        apt install -y ccache python3-dev binaryen

        apt install -y python3-numpy python3-setuptools utils-misc gapi
        apt install -y python3-lxml python3-graphviz python3-flake8
        apt install -y  python3-hgapi
        apt install -y python3-python-utils
        # pip3 install beautifulsoup4 pylint
EOF
        )
        ->withPrefix($opencv_prefix)
        ->withBuildLibraryHttpProxy(true)
        ->withBuildScript(
            <<<EOF


        mkdir -p build
        cd  build

        cmake .. \
        -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$opencv_prefix} \
        -DOPENCV_EXTRA_MODULES_PATH="../opencv_contrib/modules" \
        -DCMAKE_CXX_STANDARD=14 \
        -DCMAKE_C_STANDARD=11 \
        -DCMAKE_BUILD_TYPE=Release \
        -DBUILD_STATIC_LIBS=OFF \
        -DBUILD_SHARED_LIBS=ON \
        -DOPENCV_DOWNLOAD_PATH={$buildDir}/opencv-download-cache \
        -DOpenCV_STATIC=OFF \
        -DENABLE_PIC=ON \
        -DWITH_FFMPEG=ON \
        -DOPENCV_GENERATE_PKGCONFIG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_PERF_TESTS=OFF \
        -DBUILD_EXAMPLES=OFF \
        -DBUILD_opencv_apps=ON \
        -DBUILD_opencv_js=ON \
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

        ninja

        ninja install
EOF
        )
        //->withDependentLibraries('opencl', 'ffmpeg')
        ->withPkgName('opencv5')
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

/*
 *
 * Libva is an implementation for VA-API (Video Acceleration API)
 *
 *
 */
