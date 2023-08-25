<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $bcg729_prefix = BCG729_PREFIX;
    $lib = new Library('bcg729');
    $lib->withHomePage('https://www.linphone.org/technical-corner/bcg729')
        ->withLicense('https://gitlab.linphone.org/BC/public/bcg729/-/blob/master/LICENSE.txt', Library::LICENSE_GPL)
        ->withManual('https://github.com/BelledonneCommunications/bcg729.git')
        ->withFile('bcg729-1.1.1.tar.gz')
        ->withDownloadScript(
            'bcg729',
            <<<EOF
                git clone -b 1.1.1 --depth=1 https://gitlab.linphone.org/BC/public/bcg729.git
EOF
        )
        ->withPrefix($bcg729_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$bcg729_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('libbcg729')
    ;

    $p->addLibrary($lib);
};
