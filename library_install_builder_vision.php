<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;



function install_aom(Preprocessor $p)
{
    $aom_prefix = '/usr/aom';
    $lib = new Library('aom');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($aom_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($aom_prefix)
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
        ->withPkgName('');

    $p->addLibrary($lib);
}


function install_av1(Preprocessor $p)
{
    $av1_prefix = '/usr/av1';
    $lib = new Library('av1');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($av1_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($av1_prefix)
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
        ->withPkgName('');

    $p->addLibrary($lib);
}

function install_libvpx(Preprocessor $p)
{
    $libvpx_prefix = '/usr/libvpx';
    $lib = new Library('libvpx');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($libvpx_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libvpx_prefix)
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
        ->withPkgName('libvpx');

    $p->addLibrary($lib);
}

function install_libopus(Preprocessor $p)
{
    $libopus_prefix = '/usr/libopus';
    $lib = new Library('libopus');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($libopus_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libopus_prefix)
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
        ->withPkgName('libopus');

    $p->addLibrary($lib);
}

function install_libx264(Preprocessor $p)
{
    $libx264_prefix = '/usr/libx264';
    $lib = new Library('libx264');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($libx264_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libx264_prefix)
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
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense('https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        ->withFile('ffmpeg-n6.0.tar.gz')
        ->withSkipDownload()
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($libx265_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libx265_prefix)
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
        ->withPkgName('libx265');

    $p->addLibrary($lib);
}


function install_opencv(Preprocessor $p)
{
    $opencv_prefix = '/usr/libgomp';
    $lib = new Library('opencv');
    $lib->withHomePage('https://gcc.gnu.org/projects/gomp/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('')
        ->withSkipDownload()
        ->withManual('https://gcc.gnu.org/onlinedocs/libgomp/')
        ->withPrefix($opencv_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($opencv_prefix)
        ->withConfigure(
            <<<EOF
./configure --help
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
        ->withManual('https://ffmpeg.org/documentation.html')
        ->withPrefix($ffmpeg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($ffmpeg_prefix)
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
        ->withPkgName('');

    $p->addLibrary($lib);
}
