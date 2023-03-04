<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_rav1e(Preprocessor $p)
{
    $rav1e_prefix = '/usr/rav1e';
    $lib = new Library('rav1e');
    $lib->withHomePage('https://github.com/xiph/rav1e.git')
        ->withLicense('https://github.com/xiph/rav1e/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withUrl('github.com/xiph/rav1e/archive/refs/tags/p20230221.tar.gz')
        ->withFile('rav1e-p20230221.tar.gz')
        ->withSkipDownload()
        ->withManual('https://github.com/xiph/rav1e.git')
        ->withPrefix($rav1e_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($rav1e_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('');

    $p->addLibrary($lib);
}

function install_aom(Preprocessor $p)
{
    $aom_prefix = '/usr/aom';
    $lib = new Library('aom');
    $lib->withHomePage('https://aomedia.googlesource.com/aom')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://aomedia.googlesource.com/aom')
        ->withPrefix($aom_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($aom_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('');

    $p->addLibrary($lib);
}


function install_av1(Preprocessor $p)
{
    $av1_prefix = '/usr/av1';
    $lib = new Library('av1');
    $lib->withHomePage('https://gitlab.com/AOMediaCodec/SVT-AV1.git')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://gitlab.com/AOMediaCodec/SVT-AV1.git')
        ->withPrefix($av1_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($av1_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('');

    $p->addLibrary($lib);
}

function install_libvpx(Preprocessor $p)
{
    $libvpx_prefix = '/usr/libvpx';
    $lib = new Library('libvpx');
    $lib->withHomePage('https://chromium.googlesource.com/webm/libvpx')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://chromium.googlesource.com/webm/libvpx')
        ->withPrefix($libvpx_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libvpx_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('libvpx');

    $p->addLibrary($lib);
}

function install_libopus(Preprocessor $p)
{
    $libopus_prefix = '/usr/libopus';
    $lib = new Library('libopus');
    $lib->withHomePage('https://opus-codec.org/')
        ->withLicense('https://opus-codec.org/license', Library::LICENSE_LGPL)
        ->withUrl('https://archive.mozilla.org/pub/opus/opus-1.3.1.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://opus-codec.org/docs')
        ->withPrefix($libopus_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libopus_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('libopus');

    $p->addLibrary($lib);
}

function install_libx264(Preprocessor $p)
{
    $libx264_prefix = '/usr/libx264';
    $lib = new Library('libx264');
    $lib->withHomePage('https://www.videolan.org/developers/x264.html')
        ->withLicense('https://code.videolan.org/videolan/x264/-/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withUrl('https://code.videolan.org/videolan/x264/-/archive/master/x264-master.tar.bz2')
        ->withFile('x264-master.tar.bz2')
        ->withSkipDownload()
        ->withManual('https://code.videolan.org/videolan/x264.git')
        ->withPrefix($libx264_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libx264_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('libx264');

    $p->addLibrary($lib);
}

function install_mp3lame(Preprocessor $p)
{
    $mp3lame_prefix = '/usr/mp3lame';
    $lib = new Library('mp3lame');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($mp3lame_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($mp3lame_prefix)
        ->withConfigure(
            <<<EOF
exit 0 
./configure --help
test -d ffmpeg || git clone  https://github.com/FFmpeg/FFmpeg ffmpeg  --depth=1 --progress
test -d ffmpeg  && git -C ffmpeg  pull  --depth=1 --progress --rebase=true
test -d SVT-AV1 || git clone https://gitlab.com/AOMediaCodec/SVT-AV1.git --depth=1 --progress
test -d SVT-AV1 && git -C SVT-AV1  pull  --depth=1 --progress --rebase=true
test -d aom || git clone https://aomedia.googlesource.com/aom  --depth=1 --progress
test -d aom && git -C aom  pull   --depth=1 --progress --rebase=true
EOF
        )
        ->withPkgName('mp3lame');

    $p->addLibrary($lib);
}

function install_libx265(Preprocessor $p)
{
    $libx265_prefix = '/usr/libx265';
    $lib = new Library('libx265');
    $lib->withHomePage('https://www.videolan.org/developers/x265.html')
        ->withLicense('https://bitbucket.org/multicoreware/x265_git/src/master/COPYING', Library::LICENSE_LGPL)
        ->withUrl('http://ftp.videolan.org/pub/videolan/x265/x265_2.7.tar.gz')
        ->withFile('x265_2.7.tar.gz')
        ->withSkipDownload()
        ->withManual('https://bitbucket.org/multicoreware/x265_git.git')
        ->withPrefix($libx265_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libx265_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('libx265');

    $p->addLibrary($lib);
}


function install_opencv_contrib(Preprocessor $p)
{
    $opencv_prefix = OPENCV_PREFIX;
    $lib = new Library('opencv_contrib');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withSkipDownload()
        ->withUntarArchiveCommand('')
        ->withPrefix($opencv_prefix)
        ->withBuildScript(
            <<<EOF
            apk add python3 py3-pip  ccache
            pip3 install numpy  -i https://pypi.tuna.tsinghua.edu.cn/simple
            test -d opencv_contrib || git clone -b 5.x  https://github.com/opencv/opencv_contrib.git --depth 1 --progress
            test -d opencv || git clone -b 5.x  https://github.com/opencv/opencv.git --depth 1 --progress
EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
}

function install_opencv(Preprocessor $p)
{
    $opencv_prefix = OPENCV_PREFIX;
    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $lib = new Library('opencv');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withSkipDownload()
        ->withUntarArchiveCommand('')
        ->withPrefix($opencv_prefix)
        //->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($opencv_prefix)
        ->withBuildScript(
            <<<EOF
 
        test -d opencv || git clone -b 5.x  https://github.com/opencv/opencv.git --depth 1 --progress
        
        opencv_contrib={$buildDir}/opencv/opencv_contrib
        cd opencv
        mkdir -p build
        cd  build
        pwd
        
        cmake -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$opencv_prefix} \
        -DOPENCV_EXTRA_MODULES_PATH="../../opencv_contrib/modules" \
        -DCMAKE_BUILD_TYPE=Release \
        -DWITH_FFMPEG=ON \
        -DOPENCV_GENERATE_PKGCONFIG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_PERF_TESTS=OFF \
        -DBUILD_EXAMPLES=OFF \
        -DBUILD_opencv_apps=OFF \
        -DBUILD_SHARED_LIBS=OFF \
        ..
      
        ninja
        ninja install
EOF
        )
        ->withPkgName('opencv');

    $p->addLibrary($lib);
}

function install_ffmpeg(Preprocessor $p)
{
    $ffmpeg_prefix = '/usr/ffmpeg';
    $lib = new Library('ffmpeg');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withManual('https://trac.ffmpeg.org/wiki/CompilationGuide')
        ->withPrefix($ffmpeg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ffmpeg_prefix)
        ->withScriptBeforeConfigure('
        # 汇编编译器
        # apk add yasm nasm
        ')
        ->withConfigure(
            <<<EOF
        ./configure --help
        ./configure  --prefix=$ffmpeg_prefix

EOF
        )
        ->withPkgName('libavcodec  libavdevice  libavfilter  libavformat libavutil  libswresample  libswscale')
        ->withBinPath($ffmpeg_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
}

function install_graphviz(Preprocessor $p)
{
    $graphviz_prefix = '/usr/graphviz';
    $lib = new Library('graphviz');
    $lib->withHomePage('https://www.graphviz.org/about/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://gitlab.com/graphviz/graphviz/-/archive/main/graphviz-main.tar.gz')
        ->withManual('https://www.graphviz.org/download/')
        ->withManual('https://www.graphviz.org/documentation/')
        ->withPrefix($graphviz_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($graphviz_prefix)
        ->withConfigure(
            <<<EOF
        ./autogen.sh
        ./configure --help

        ./configure  --prefix=$graphviz_prefix \
        --enable-static=yes \
        --enable-shared=no


EOF
        )
        ->withPkgName('libavcodec  libavdevice  libavfilter  libavformat libavutil  libswresample  libswscale')
        ->withBinPath($graphviz_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
}


# https://mirrors.tuna.tsinghua.edu.cn/help/CTAN/
function install_TeX(Preprocessor $p)
{
    $TeX_prefix = '/usr/TeX';
    $lib = new Library('TeX');
    $lib->withHomePage('https://www.ctan.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_SPEC)
        ->withUrl('https://mirrors.tuna.tsinghua.edu.cn/CTAN/systems/texlive/tlnet/install-tl.zip')
        ->withManual('https://www.graphviz.org/download/')
        ->withManual('https://www.graphviz.org/documentation/')
        ->withUntarArchiveCommand('unzip')
        ->withPrefix($TeX_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($TeX_prefix)
        ->withBuildScript(
            <<<EOF
        cd install-tl-* 
        ls -lh 
        perl install-tl --repository https://mirrors.tuna.tsinghua.edu.cn/CTAN/systems/texlive/tlnet
EOF
        )

        ->withPkgName('')
        ->withBinPath($TeX_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
}
