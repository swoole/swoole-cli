<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsamplerate_prefix = LIBSAMPLERATE_PREFIX;
    $lib = new Library('libsamplerate');
    $lib->withHomePage('http://libsndfile.github.io/libsamplerate/')
        ->withLicense('https://github.com/libsndfile/libsamplerate/blob/master/COPYING', Library::LICENSE_BSD)
        ->withManual('https://github.com/libsndfile/libsamplerate.git')
        ->withFile('libsamplerate-latest.tar.gz')
        ->withDownloadScript(
            'libsamplerate',
            <<<EOF
                git clone -b master --depth=1 https://github.com/libsndfile/libsamplerate.git
EOF
        )
        ->withPrefix($libsamplerate_prefix)
        ->withConfigure(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libsamplerate_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_TESTING=NO \
            -DLIBSAMPLERATE_EXAMPLES=OFF \
            -DLIBSAMPLERATE_INSTALL=YES

EOF
        )
        ->withPkgName('libsamplerate')
        ->withBinPath($libsamplerate_prefix . '/bin/')
    ;
    $p->addLibrary($lib);

};
