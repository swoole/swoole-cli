<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openexr_prefix = OPENEXR_PREFIX;
    $imath_prefix = IMATH_PREFIX;
    $libdeflate_prefix = LIBDEFLATE_PREFIX;
    $lib = new Library('openexr');
    $lib->withHomePage('https://openexr.com/en/latest/')
        ->withLicense('https://openexr.com/en/latest/license.html#license', Library::LICENSE_BSD)
        ->withManual('https://openexr.com/en/latest/install.html#install')
        ->withFile('openexr-latest.tar.gz')
        ->withDownloadScript(
            'openexr',
            <<<EOF
            git clone -b main  --depth=1 https://github.com/AcademySoftwareFoundation/openexr.git
EOF
        )
        //->withAutoUpdateFile()
        ->withPrefix($openexr_prefix)
        ->withBuildLibraryHttpProxy()
        ->withBuildScript(
            <<<EOF
        mkdir -p build
        cd build
        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$openexr_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$imath_prefix};{$libdeflate_prefix}" \
        -DBUILD_TESTING=OFF \
        -DOPENEXR_INSTALL_EXAMPLES=ON

        cmake --build . --config Release

        cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('OpenEXR')
        ->withBinPath($openexr_prefix . '/bin/')
        ->withDependentLibraries('libdeflate', 'imath')
    ;
    $p->addLibrary($lib);
};
