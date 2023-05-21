<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libgif_prefix = GIF_PREFIX;
    $p->addLibrary(
        (new Library('libgif'))
            ->withHomePage('https://giflib.sourceforge.net/')
            ->withManual('https://giflib.sourceforge.net/intro.html')
            ->withLicense('https://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withMd5sum('6f03aee4ebe54ac2cc1ab3e4b0a049e5')
            ->withPrefix($libgif_prefix)
            ->withMakeOptions('libgif.a')
            ->withMakeInstallCommand('')
            ->withScriptAfterInstall(
                <<<EOF
                if [ ! -d {$libgif_prefix}/lib ]; then
                    mkdir -p {$libgif_prefix}/lib
                fi
                if [ ! -d {$libgif_prefix}/include ]; then
                    mkdir -p {$libgif_prefix}/include
                fi
                cp libgif.a {$libgif_prefix}/lib/libgif.a
                cp gif_lib.h {$libgif_prefix}/include/gif_lib.h
EOF
            )
            ->withLdflags('-L' . $libgif_prefix . '/lib')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libgif_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libgif_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lgif');
};
