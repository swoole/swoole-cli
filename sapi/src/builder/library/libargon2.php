<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libargon2_prefix = LIBARGON2_PREFIX;
    $lib = new Library('libargon2');
    $lib->withHomePage('https://github.com/P-H-C/phc-winner-argon2.git')
        ->withLicense('https://github.com/P-H-C/phc-winner-argon2/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/P-H-C/phc-winner-argon2.git')
        ->withFile('phc-winner-argon2-latest.tar.gz')
        ->withDownloadScript(
            'phc-winner-argon2',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/P-H-C/phc-winner-argon2.git
EOF
        )
        ->withPrefix($libargon2_prefix)
        ->withMakeOptions('PREFIX=' . $libargon2_prefix)
        ->withMakeInstallOptions('PREFIX=' . $libargon2_prefix)
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libargon2_prefix}/lib/x86_64-linux-gnu/*.so.*
            rm -rf {$libargon2_prefix}/lib/x86_64-linux-gnu/*.so
            rm -rf {$libargon2_prefix}/lib/x86_64-linux-gnu/*.dylib
EOF
        )
        ->withPkgName('libargon2')
        ->withBinPath($libargon2_prefix . '/bin/')
        ->withLdflags('-L' . $libargon2_prefix . '/lib/x86_64-linux-gnu/')
        ->withPkgConfig($libargon2_prefix . '/lib/x86_64-linux-gnu/pkgconfig');

    $p->addLibrary($lib);
};
