<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $re2_prefix = RE2_PREFIX;
    $absl_prefix = ABSL_PREFIX;

    $lib = new Library('re2');
    $lib->withHomePage('https://github.com/google/re2.git')
        ->withLicense('https://github.com/google/re2/blob/main/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/google/re2.git')
        ->withUrl('https://github.com/google/re2/releases/download/2024-04-01/re2-2024-04-01.tar.gz')
        ->withFile('re2-2024-04-01.tar.gz')
        ->withPrefix($re2_prefix)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build
         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$re2_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$absl_prefix}"

        cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('re2')
    ;
    $p->addLibrary($lib);
};
