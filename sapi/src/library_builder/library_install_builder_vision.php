<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_rav1e(Preprocessor $p)
{
}

function install_aom(Preprocessor $p)
{
}


function install_av1(Preprocessor $p)
{
}

function install_libvpx(Preprocessor $p)
{
}

function install_libopus(Preprocessor $p)
{
}

function install_libx264(Preprocessor $p)
{
}

function install_numa(Preprocessor $p)
{
}

function install_mp3lame(Preprocessor $p)
{
}

function install_libx265(Preprocessor $p)
{
}


function install_opencv_contrib(Preprocessor $p)
{
}

function install_opencv(Preprocessor $p)
{
}

function install_ffmpeg(Preprocessor $p)
{
}


function install_graphviz(Preprocessor $p)
{
    $graphviz_prefix = GRAPHVIZ_PREFIX;
    $lib = new Library('graphviz');
    $lib->withHomePage('https://www.graphviz.org/about/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Library::LICENSE_LGPL
        )
        ->withUrl('https://gitlab.com/graphviz/graphviz/-/archive/main/graphviz-main.tar.gz')
        ->withUrl(
            'https://gitlab.com/api/v4/projects/4207231/packages/generic/graphviz-releases/8.0.2/graphviz-8.0.2.tar.gz'
        )
        ->withManual('https://www.graphviz.org/download/')
        ->withManual('https://www.graphviz.org/documentation/')
        ->withFile('graphviz-8.0.2.tar.gz')
        ->withDownloadScript(
            'graphviz',
            <<<EOF
        git clone -b 8.0.2 --depth=1 --progress https://gitlab.com/graphviz/graphviz.git
EOF
        )
        ->withPrefix($graphviz_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($graphviz_prefix)
        ->withConfigure(
            <<<EOF
        ./autogen.sh
        ./configure --help

        ./configure \
         --prefix=$graphviz_prefix \
        --enable-static=yes \
        --enable-shared=no

EOF
        )
        ->withPkgName('libcdt')
        ->withPkgName('libcgraph')
        ->withPkgName('libgvc')
        ->withPkgName('libgvpr')
        ->withPkgName('liblab_gamut')
        ->withPkgName('libpathplan')
        ->withPkgName('libxdot')
        ->withBinPath($graphviz_prefix . '/bin/');

    $p->addLibrary($lib);
}


# https://mirrors.tuna.tsinghua.edu.cn/help/CTAN/
function install_TeX(Preprocessor $p)
{
    $TeX_prefix = '/usr/TeX';
    $lib = new Library('TeX');
    $lib->withHomePage('https://www.ctan.org/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Library::LICENSE_SPEC
        )
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
        ->withBinPath($TeX_prefix . '/bin/');

    $p->addLibrary($lib);
}
