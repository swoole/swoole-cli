<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $vtk_prefix = VTK_PREFIX;
    $ffmpeg_prefix = FFMPEG_PREFIX;

    $lib = new Library('vtk');
    $lib->withHomePage('https://www.vtk.org/')
        ->withLicense('https://gitlab.kitware.com/vtk/vtk/-/blob/master/Copyright.txt', Library::LICENSE_BSD)
        ->withManual('https://gitlab.kitware.com/vtk/vtk/-/blob/master/Documentation/dev/build.md#building-vtk')
        ->withManual('https://docs.vtk.org/en/latest/build_instructions/index.html')
        ->withFile('vtk-latest.tar.gz')
        ->withDownloadScript(
            'vtk',
            <<<EOF
                # git clone -b v9.2.6 --depth 1 --progress --recursive  https://gitlab.kitware.com/vtk/vtk.git
                git clone -b master --depth 1 --progress --recursive  https://gitlab.kitware.com/vtk/vtk.git

EOF
        )
        ->withPrefix($vtk_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($vtk_prefix)
        ->withBuildScript(
            <<<EOF

        mkdir -p build
        cd  build

        cmake .. \
        -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$vtk_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release \
        -DWITH_FFMPEG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_EXAMPLES=OFF \
        -DBUILD_SHARED_LIBS=OFF \
        -DFFMPEG_ROOT={$ffmpeg_prefix} \

        ninja -j {$p->getMaxJob()}
        ninja install
EOF
        )
        ->withPkgName('vtk')
        ->withDependentLibraries(
            'ffmpeg',
            //'libosmesa'
            //'open_mpi'
        )
    ;

    $p->addLibrary($lib);
};
