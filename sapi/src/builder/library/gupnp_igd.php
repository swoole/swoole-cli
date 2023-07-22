<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $GUPnP_prefix = GUPnP_PREFIX;
    $lib = new Library('gupnp_igd');
    $lib->withHomePage('https://wiki.gnome.org/Projects/GUPnP')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://download.gnome.org/sources/gupnp-igd/1.6/gupnp-igd-1.6.0.tar.xz')
        ->withManual('https://wiki.gnome.org/Projects/GUPnP')
        ->withUntarArchiveCommand('xz')
        ->withPrefix($GUPnP_prefix)
        ->withBuildScript(
            <<<EOF
            # https://download.gnome.org/sources/

EOF
        )
    ;

    $p->addLibrary($lib);
};
