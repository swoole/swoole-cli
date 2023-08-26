<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libgomp_prefix = LIBGOMP_PREFIX;

    # OpenMP（libgomp）

    $lib = new Library('libgomp');
    $lib->withHomePage('https://www.openmp.org/')
         ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
         ->withUrl('')
         ->withManual('https://www.openmp.org/specifications/')
         ->withPrefix($libgomp_prefix)
         ->withCleanBuildDirectory()
         ->withCleanPreInstallDirectory($libgomp_prefix)

        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withFile('opencv-4.7.0.tar.gz')
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
          return 0

EOF
        )

    ;

    $p->addLibrary($lib);
};
