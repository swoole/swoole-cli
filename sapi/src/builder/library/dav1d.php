<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dav1d_prefix = DAV1D_PREFIX;
    $p->addLibrary(
        (new Library('dav1d'))
            ->withHomePage('https://code.videolan.org/videolan/dav1d/')
            ->withLicense('https://code.videolan.org/videolan/dav1d/-/blob/master/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://code.videolan.org/videolan/dav1d/-/archive/1.1.0/dav1d-1.1.0.tar.gz')
            ->withFile('dav1d-1.1.0.tar.gz')
            ->withManual('https://code.videolan.org/videolan/dav1d')
            ->withPrefix($dav1d_prefix)
            ->withBuildScript(
                <<<EOF
                # apk add ninja python3 py3-pip  nasm
                # pip3 install meson
                mkdir -p build
                cd build
                meson setup \
                --backend=ninja \
                --prefix={$dav1d_prefix} \
                --default-library=static \
                ..
                ninja
                ninja install


EOF
            )
            ->withPkgName('dav1d')
            ->withBinPath($dav1d_prefix . '/bin/')
    );
};
