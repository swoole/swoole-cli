<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libfvad_prefix = LIBFVAD_PREFIX;

    $lib = new Library('libfvad');
    $lib->withHomePage('https://github.com/dpirch/libfvad.git')
        ->withLicense('https://github.com/dpirch/libfvad/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/dpirch/libfvad.git')
        ->withDownloadScript(
            'libfvad',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/dpirch/libfvad.git
EOF
        )
        ->withBuildCached(false)
        ->withPrefix($libfvad_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libfvad_prefix)
        ->withConfigure(
            <<<EOF
        mkdir -p build
        cd build
        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libfvad_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON
EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
                mkdir -p {$libfvad_prefix}/lib/pkgconfig/
                cat > {$libfvad_prefix}/lib/pkgconfig/libfvad.pc <<'__libfvad__EOF'
prefix={$libfvad_prefix}
exec_prefix=\${prefix}
libdir=\${exec_prefix}/lib
includedir=\${prefix}/include

Name: libfvad
Description: Voice activity detection (VAD) library
URL: https://github.com/dpirch/libfvad.git
Version: v1.0
Libs: -L\${libdir} -lfvad
Cflags: -I\${includedir}

__libfvad__EOF
EOF
        )
        ->withPkgName('libfvad')
    ;

    $p->addLibrary($lib);

};
