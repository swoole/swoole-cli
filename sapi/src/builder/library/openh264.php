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
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build_dir \
            --prefix={$openh264_prefix} \
            --libdir={$openh264_prefix}/lib \
            --includedir={$openh264_prefix}/include \
            --default-library=static \
            --backend=ninja \
            --prefer-static \
            -Dtests=disabled \

            # meson compile -v
            # cat build.ninja | grep "command ="
            # ninja -t commands

            ninja -C build_dir
            ninja -C build_dir install

EOF
        )
        ->withPkgName('openh264');

    $p->addLibrary($lib);
};
