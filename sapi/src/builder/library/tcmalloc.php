<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $tcmalloc_preifx = TCMALLOC_PREFIX;
    $p->addLibrary(
        (new Library('tcmalloc'))
            ->withHomePage('https://google.github.io/tcmalloc/overview.html')
            ->withLicense('https://github.com/google/tcmalloc/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withUrl('https://github.com/google/tcmalloc/archive/refs/heads/master.zip')
            ->withFile('tcmalloc.zip')
            ->withPrefix($tcmalloc_preifx)
            ->withUntarArchiveCommand('unzip')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                # apk add bazel
                # https://pkgs.alpinelinux.org/packages?name=bazel*&branch=edge&repo=testing&arch=&maintainer=
                cd  tcmalloc-master/
                bazel help
                bazel build
                return
                ./configure \
                --prefix={$tcmalloc_preifx} \
                --enable-static \
                --disable-shared
EOF
            )
            ->withPkgName('tcmalloc')

    );
};
