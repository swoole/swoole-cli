<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openh264_prefix = OPENH264_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('openh264');
    $lib->withHomePage('https://github.com/cisco/openh264.git')
        ->withLicense('https://github.com/cisco/openh264/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withUrl('https://github.com/cisco/openh264/archive/refs/tags/v2.3.1.tar.gz')
        ->withManual('https://github.com/cisco/openh264.git')
        ->withFile('openh264-v2.3.1.tar.gz')
        ->withPrefix($openh264_prefix)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
apt install -y nasm

EOF
        )
        ->withConfigure(
            <<<EOF
              meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build_dir \
            -Dprefix={$openh264_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dtests=disabled \



            meson compile -C build_dir

            ninja -C build_dir
            ninja -C build_dir install
EOF
        )
        ->withBuildLibraryCached(false)
        ->withPkgName('openh264')
        ->withDependentLibraries('libpcap', 'openssl')
        ->withLdflags('-L' . $openh264_prefix . '/lib/x86_64-linux-gnu/')
        ->withPkgConfig($openh264_prefix . '/lib/x86_64-linux-gnu/pkgconfig')
    ;

    $p->addLibrary($lib);
};
