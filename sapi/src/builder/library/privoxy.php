<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $privoxy_prefix = PRIVOXY_PREFIX;
    $p->addLibrary(
        (new Library('privoxy'))
            ->withHomePage('https://www.privoxy.org')
            ->withManual('https://www.privoxy.org/gitweb/?p=privoxy.git')
            ->withManual('https://www.privoxy.org/user-manual/quickstart.html')
            ->withManual('https://www.privoxy.org/user-manual/installation.html')
            ->withLicense('https://www.privoxy.org/gitweb/?p=privoxy.git;a=blob_plain;f=LICENSE.GPLv3;h=f288702d2fa16d3cdf0035b15a9fcbc552cd88e7;hb=HEAD', Extension::LICENSE_GPL)
            ->withUrl('https://sourceforge.net/projects/ijbswa/files/Sources/3.0.34%20(stable)/privoxy-3.0.34-stable-src.tar.gz')
            ->withDownloadScript(
                'privoxy',
                <<<EOF
                    git clone https://www.privoxy.org/git/privoxy.git
EOF
            )
            ->withFile('privoxy.tar.gz')
            ->withPrefix($privoxy_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($privoxy_prefix)
            ->withBuildScript(
                <<<EOF
                  autoheader
                  autoconf
                  ./configure


 EOF
            )
    );
};
