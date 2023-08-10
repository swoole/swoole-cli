<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libdrm_prefix = LIBDRM_PREFIX;
    $lib = new Library('libdrm');
    $lib->withHomePage('https://www.linuxfromscratch.org/blfs/view/svn/x/libdrm.html')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://www.linuxfromscratch.org/blfs/view/svn/x/libdrm.html')
        ->withUrl('https://dri.freedesktop.org/libdrm/libdrm-2.4.115.tar.xz')
        ->withUntarArchiveCommand('xz')
        ->withPrefix($libdrm_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libdrm_prefix)
        /** 使用 meson、ninja  构建 start **/
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$libdrm_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dudev=true           \
            -Dvalgrind=disabled

            ninja -C build
            ninja -C build install
EOF
        )
        ->withPkgName('libdrm')
        ->withPkgName('libdrm_amdgpu')
        ->withPkgName('libdrm_nouveau')
        ->withPkgName('libdrm_radeon')
        ->withBinPath($libdrm_prefix . '/bin/')

    ;

    $p->addLibrary($lib);

};
