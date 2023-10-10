<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libx264_prefix = LIBX264_PREFIX;
    $lib = new Library('libx264');
    $lib->withHomePage('https://www.videolan.org/developers/x264.html')
        ->withManual('https://code.videolan.org/videolan/x264.git')
        ->withLicense('https://code.videolan.org/videolan/x264/-/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withFile('libx264-stable.tar.gz')
        ->withDownloadScript(
            'x264',
            <<<EOF
        git clone -b stable --progress --depth=1  https://code.videolan.org/videolan/x264.git
EOF
        )
        ->withPrefix($libx264_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libx264_prefix)
        ->withConfigure(
            <<<EOF
        ./configure --help
        ./configure \
        --prefix={$libx264_prefix} \
        --enable-static \
        --enable-pic \
        --enable-lto \
        --enable-strip

EOF
        )
        ->withPkgName('x264')
        ->withBinPath($libx264_prefix . '/bin/');

    $p->addLibrary($lib);
};
