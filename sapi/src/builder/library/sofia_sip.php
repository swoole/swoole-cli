<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $sofia_sip_prefix = SOFIA_SIP_PREFIX;
    $lib = new Library('sofia_sip');
    $lib->withHomePage('https://github.com/freeswitch/spandsp.git')
        ->withLicense('https://github.com/freeswitch/sofia-sip/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/freeswitch/sofia-sip.git')
        ->withFile('sofia-sip-v1.13.16.tar.gz')
        ->withDownloadScript(
            'sofia-sip',
            <<<EOF
                git clone -b v1.13.16  --depth=1 https://github.com/freeswitch/sofia-sip.git
EOF
        )
        ->withPrefix($sofia_sip_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help
            PACKAGES="openssl"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
             --prefix={$sofia_sip_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-openssl \
            --without-doxygen
EOF
        )
        ->withPkgName('sofia-sip-ua')
        ->withBinPath($sofia_sip_prefix . '/bin/')
        ->withDependentLibraries('openssl');
    ;

    $p->addLibrary($lib);
};
