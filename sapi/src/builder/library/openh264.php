<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openh264_prefix = OPENH264_PREFIX;
    $lib_path = $p->getOsType() == 'macos' ? $openh264_prefix . "/lib/" : $openh264_prefix . '/lib/x86_64-linux-gnu/';
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
            apk add nasm

EOF
        )
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            apk add nasm

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


            ninja -C build_dir
            ninja -C build_dir install
EOF
        )
        ->withPkgName('openh264')
        ->withDependentLibraries('libpcap', 'openssl')
        ->withLdflags('-L' . $lib_path)
        ->withPkgConfig($lib_path . '/pkgconfig');

    $p->addLibrary($lib);
};
