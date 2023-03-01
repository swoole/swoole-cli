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
        ->withCleanInstallDirectory($rav1e_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('');

    $p->addLibrary($lib);
}

function install_libyuv(Preprocessor $p)
{
    $libyuv_prefix = '/usr/libyuv';
    $lib = new Library('libyuv');
    $lib->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
        ->withLicense('https://github.com/xiph/rav1e/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withUrl('')
        ->withSkipDownload()
        ->withManual('https://chromium.googlesource.com/libyuv/libyuv/+/HEAD/docs/getting_started.md')
        ->withPrefix($libyuv_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libyuv_prefix)
        ->withBuildScript(
            <<<EOF
        mkdir out
        cd out
        cmake -DCMAKE_INSTALL_PREFIX="/usr/lib" -DCMAKE_BUILD_TYPE="Release" ..
        cmake --build . --config Release
        sudo cmake --build . --target install --config Release

EOF
        )

        ->withPkgName('libyuv');

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
        ->withCleanInstallDirectory($aom_prefix)
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
        ->withCleanInstallDirectory($av1_prefix)
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
        ->withCleanInstallDirectory($libvpx_prefix)
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
        ->withCleanInstallDirectory($libopus_prefix)
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
        ->withCleanInstallDirectory($libx264_prefix)
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
        ->withCleanInstallDirectory($mp3lame_prefix)
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
        ->withCleanInstallDirectory($libx265_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('libx265');

    $p->addLibrary($lib);
}


function install_opencv(Preprocessor $p)
{
    $opencv_prefix = '/usr/opencv';
    $lib = new Library('opencv');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withUntarArchiveCommand('')
        ->withPrefix($opencv_prefix)
        //->withCleanBuildDirectory()
        ->withCleanInstallDirectory($opencv_prefix)
        ->withScriptBeforeConfigure(
            <<<EOF
            test -d opencv || git clone -b 5.x  https://github.com/opencv/opencv.git --depth 1 --progress
            test -d opencv_contrib || git clone -b 5.x  https://github.com/opencv/opencv_contrib.git --depth 1 --progress
            apk add python3 py3-pip  ccache
            pip3 install numpy  -i https://pypi.tuna.tsinghua.edu.cn/simple 
EOF
        )
        ->withBuildScript(
            <<<EOF
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
        ->withCleanInstallDirectory($ffmpeg_prefix)
        ->withConfigure(
            <<<EOF
exit 0 

EOF
        )
        ->withPkgName('');

    $p->addLibrary($lib);
}
