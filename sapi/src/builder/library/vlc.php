<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $vlc_prefix = VLC_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('vlc');
    $lib->withHomePage('https://www.videolan.org/vlc/libvlc.html')
        ->withLicense('http://www.gnu.org/copyleft/gpl.html', Library::LICENSE_GPL)
        ->withUrl('https://get.videolan.org/vlc/3.0.18/vlc-3.0.18.tar.xz')
        ->withManual('https://github.com/videolan/vlc')
        ->withManual('https://wiki.videolan.org/')
        ->withManual('https://www.videolan.org/developers/vlc.html')
        ->withManual('https://github.com/videolan/vlc/blob/master/meson_options.txt')
        ->withBuildLibraryCached(false)
        ->withUntarArchiveCommand('xz')
        ->withPrefix($example_prefix)
        ->withCleanPreInstallDirectory($example_prefix)

        ->withBuildScript(
            <<<EOF

            meson setup  build \
            -Dprefix={$example_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dx11=disabled \
            -Dxcb=disabled

            ninja -C build
            ninja -C build install
EOF
        )
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries(
            'libpcap',
            'openssl',
            'libogg',
            "ncurses",
            "libjpeg",
            "libpng",
            "libxml2",
            //"upnp",
            "dav1d",
            "aom",
            "libvpx",
            "fdk_aac",
            "libx264",
            "libx265",
            "libopus",
            "flac",
            "freetype",
            "ffmpeg",
            "wayland",
            'wayland_protocols',
            'alsa'
        )
    ;

    $p->addLibrary($lib);

};
