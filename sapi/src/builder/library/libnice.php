<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libnice_prefix = LIBNICE_PREFIX;
    $lib = new Library('libnice');
    $lib->withHomePage('https://libnice.freedesktop.org/')
        ->withLicense('https://gitlab.com/libnice/libnice/-/blob/master/COPYING', Library::LICENSE_GPL)
        ->withUrl('https://libnice.freedesktop.org/releases/libnice-0.1.21.tar.gz')
        ->withManual('https://gitlab.com/libnice/libnice.git')
        ->withPrefix($libnice_prefix)
        ->withPreInstallCommand('debian',
            <<<EOF
            apt install -y ninja-build python3-pip meson
EOF
        )
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$libnice_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=disabled \
            -Dgtk_doc=disabled \

            meson compile -C build

            ninja -C build
            ninja -C build install
EOF
        )
        ->withDependentLibraries('openssl', 'gstreamer')
    ;

    $p->addLibrary($lib);
};

/*

ICE
  https://tools.ietf.org/html/rfc5245 (old)
  https://tools.ietf.org/html/rfc8445
STUN
  https://tools.ietf.org/html/rfc3489 (old)
  https://tools.ietf.org/html/rfc5389
STUN Consent Freshness RFC
  https://tools.ietf.org/html/rfc7675
TURN
  https://tools.ietf.org/html/rfc5766
RTP
  https://tools.ietf.org/html/rfc3550
ICE-TCP RFC
  https://tools.ietf.org/html/rfc6544
Trickle ICE
   https://tools.ietf.org/html/draft-ietf-ice-trickle-21
XMPP Jingle ICE transport
  https://www.xmpp.org/extensions/xep-0176.html

In future, nice may additionally support the following standards.

NAT-PMP
  http://files.dns-sd.org/draft-cheshire-nat-pmp.txt



 */
