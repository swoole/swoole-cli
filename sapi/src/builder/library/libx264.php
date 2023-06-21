<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libx264_prefix = LIBX264_PREFIX;
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
        ./configure --help
        ./configure \
        --prefix={$libx264_prefix} \
        --enable-static

EOF
        )
        ->withPkgName('x264')
        ->withBinPath($libx264_prefix . '/bin/');

    $p->addLibrary($lib);
};
