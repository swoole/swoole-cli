<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openh264_prefix = OPENH264_PREFIX;

    $lib = new Library('openh264');
    $lib->withHomePage('https://github.com/cisco/openh264.git')
        ->withLicense('https://github.com/cisco/openh264/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/cisco/openh264.git')
        ->withUrl('https://github.com/cisco/openh264/archive/refs/tags/v2.6.0.tar.gz')
        ->withfile('openh264-v2.6.0.tar.gz')
        ->withPrefix($openh264_prefix)
        ->withConfigure(
            <<<EOF
              meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build_dir \
            -Dprefix={$openh264_prefix} \
            -Dlibdir={$openh264_prefix}/lib \
            -Dincludedir={$openh264_prefix}/include \
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
        ->withPkgName('openh264');

    $p->addLibrary($lib);
};
