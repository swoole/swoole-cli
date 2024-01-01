<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_DOCS_PREFIX;

    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $lib = new Library('opencv_docs');
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
        apk add ccache python3-dev binaryen  doxygen
        apk add zlib
        apk add libva-dev
        apk add ffmpeg
        apk add libavif libavif-dev aom-dev svt-av1-dev dav1d-dev
        apk add libva-dev
        apk add harfbuzz
        apk add glog glog-dev
        apk add gflags gflags-dev
        apk add freetype-dev

        apk add gtk4.0
        apk add gtk+2.0
        apk add gtk+2.0-dev
        apk add gtk+3.0-dev
        apk add gtk4.0-dev

        apk add vtk vtk-dev

        apk add libdc1394-dev
        apk add blas openblas openblas-dev liblapack
        apk add eigen eigen-dev
        apk add py3-pylint
        apk add tesseract-ocr tesseract-ocr-dev  # Optical Character Recognition，光学字符识别


        # Eigen Pylint  tesseract OGRE3D
        # OGRE3D  https://www.ogre3d.org/download/sdk/sdk-ogre


        pip3 install numpy setuptools utils-misc  gapi  utils lxml beautifulsoup4 graphviz pylint flake8 bs4


        # apk add binaryen # WebAssembly 的优化器和编译器/工具链
EOF
        )
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y ccache python3-dev binaryen doxygen
        apt install -y libstdc++-12-dev
        apt install -y libavif-dev libaom-dev libsvtav1-dev libdav1d-dev libgav1-dev
        apt install -y libvtk9-dev
        apt install -y libogre-1.12-dev
        apt install -y python3-flake8
        apt install -y libgflags-dev
        apt install -y libva-dev
        apt install -y libgtk-3-dev
        apt install -y libgtk-4-dev
        apt install -y ffmpeg
        apt install -y libharfbuzz-dev
        apt install -y libfreetype-dev
        apt install -y libdc1394-dev
        apt install -y  libopenblas-dev liblapack-dev
        apt install -y  libeigen3-dev
        apt install -y  tesseract-ocr libtesseract5 # Optical Character Recognition，光学字符识别
        # apt install -y  tesseract-ocr-all #

        apt install -y libogre-1.12-dev

        apt install -y libdbus-1-3 libdbus-1-dev libdbus-c++-dev libdbus-cpp-dev dbus libdbus-1-3

        apt install -y  python3-venv
        python3 -m venv /tmp/venv

        source /tmp/venv/bin/activate

        pip3 install numpy setuptools utils-misc  gapi  utils lxml beautifulsoup4 graphviz pylint flake8 bs4

        deactivate

        # Eigen Pylint  tesseract OGRE3D
        # OGRE3D  https://www.ogre3d.org/download/sdk/sdk-ogre




EOF
        )
        ->withPrefix($opencv_prefix)
        ->withBuildLibraryHttpProxy(true)
        ->withBuildScript(
            <<<EOF
        set -x
        PACKAGES='libavif '
        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)"
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"



       # source /tmp/venv/bin/activate

        mkdir -p build
        cd  build

        cmake .. \
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
        -DBUILD_EXAMPLES=ON \
        -DBUILD_opencv_apps=ON \
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
        -DWITH_GTK=ON \
        -DWITH_CUDA=OFF \
        -DAVIF_INCLUDE_DIR=/usr/include/avif/

        pip3 install bs4

        # 参考文档
        # https://docs.opencv.org/5.x/d4/db1/tutorial_documentation.html

        make doxygen

        pwd
        ls -lh doc/doxygen/html/index.html

        # deactivate
EOF
        )
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
 *   tesseract OCR，即Optical Character Recognition，光学字符识别，是指通过扫描字符
 */
