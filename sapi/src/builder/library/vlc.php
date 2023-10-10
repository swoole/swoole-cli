<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $vlc_prefix = VLC_PREFIX;

    $lib = new Library('vlc');
    $lib->withHomePage('https://www.videolan.org/vlc/libvlc.html')
        ->withLicense('http://www.gnu.org/copyleft/gpl.html', Library::LICENSE_GPL)
        ->withManual('https://github.com/videolan/vlc')
        ->withManual('https://wiki.videolan.org/')
        ->withManual('https://www.videolan.org/developers/vlc.html')
        ->withManual('https://github.com/videolan/vlc/blob/master/meson_options.txt')
        //->withUrl('https://get.videolan.org/vlc/3.0.18/vlc-3.0.18.tar.xz')
        ->withFile('vlc-latest.tar.gz')
        ->withDownloadScript(
            'vlc',
            <<<EOF
        git clone -b master --depth=1 https://github.com/videolan/vlc.git
EOF
        )
        ->withBuildLibraryCached(false)
        //->withUntarArchiveCommand('xz')
        ->withPrefix($vlc_prefix)
        ->withCleanPreInstallDirectory($vlc_prefix)
        ->withBuildScript(
            <<<EOF

            meson setup   \
            -Dprefix={$vlc_prefix} \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dx11=disabled \
            -Dxcb=disabled \
             build .

            meson -C build
            meson -C build install
EOF
        )
        ->withBinPath($vlc_prefix . '/bin/')
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
