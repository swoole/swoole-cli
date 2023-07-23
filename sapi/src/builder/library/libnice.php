<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libnice_prefix = LIBNICE_PREFIX;
    $lib = new Library('libnice');
    $lib->withHomePage('https://libnice.freedesktop.org/')
        ->withLicense('https://gitlab.com/libnice/libnice/-/blob/master/COPYING', Library::LICENSE_GPL)
        ->withUrl('https://gitlab.com/libnice/libnice/-/archive/master/libnice-master.tar.gz')
        ->withManual('https://gitlab.com/libnice/libnice.git')
        ->withDownloadScript(
            'libnice',
            <<<EOF
                git clone -b 0.1.19  --depth=1 https://gitlab.com/libnice/libnice.git
EOF
        )

        ->withPrefix($libnice_prefix)
        ->withBuildScript(
            <<<EOF
meson build_dir
ninja -C build_dir
# ninja -C build_dir test (or "meson test -C build_dir" for more control)
ninja -C build_dir install
EOF
        )
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
