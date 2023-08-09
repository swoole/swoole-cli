<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libfribidi_prefix = LIBFRIBIDI_PREFIX;
    $p->addLibrary(
        (new Library('libfribidi'))
            ->withLicense('https://github.com/fribidi/fribidi/blob/master/COPYING', Library::LICENSE_LGPL)
            ->withHomePage('https://github.com/fribidi/fribidi.git')
            ->withUrl('https://github.com/fribidi/fribidi/archive/refs/tags/v1.0.12.tar.gz')
            ->withFile('fribidi-v1.0.12.tar.gz')
            ->withLabel('library')
            ->withPrefix($libfribidi_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libfribidi_prefix)
            ->withConfigure(
                "

                # 可以使用 meson
                # meson setup  build

                sh autogen.sh
                ./configure --help

                ./configure \
                --prefix={$libfribidi_prefix} \
                --enable-static=yes \
                --enable-shared=no
            "
            )
    );
};
