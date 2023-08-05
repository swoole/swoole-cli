<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libargon2_prefix = LIBARGON2_PREFIX;

    $libargon2_lib_path = $libargon2_prefix . '/lib/';
    if (($p->getOsType() == 'linux') && ($p->getSystemArch() == 'x64')) {
        $libargon2_lib_path = $libargon2_prefix . '/lib/x86_64-linux-gnu/';
    }

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
            rm -rf {$libargon2_lib_path}/*.so.*
            rm -rf {$libargon2_lib_path}/*.so
            rm -rf {$libargon2_lib_path}/*.dylib
EOF
        )
        ->withPkgName('libargon2')
        ->withBinPath($libargon2_prefix . '/bin/')
        ->withLdflags('-L' . $libargon2_lib_path)
        ->withPkgConfig($libargon2_lib_path . "/pkgconfig");

    $p->addLibrary($lib);
};
