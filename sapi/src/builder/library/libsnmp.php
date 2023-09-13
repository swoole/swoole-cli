<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsnmp_prefix = OPENCV_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libsnmp');
    $lib->withHomePage('http://www.net-snmp.org/')
        ->withLicense('http://www.net-snmp.org/about/license.html', Library::LICENSE_BSD)
        ->withUrl('https://sourceforge.net/projects/net-snmp/files/net-snmp/5.9.1/net-snmp-5.9.1.tar.gz')
        ->withManual('http://www.net-snmp.org/docs/INSTALL.html')
        ->withBuildLibraryCached(false)
        ->withPrefix($libsnmp_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libsnmp_prefix)
        ->withConfigure(
            <<<EOF

            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$libsnmp_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withBinPath($libsnmp_prefix . '/bin/')
        ->withDependentLibraries('libpcap', 'openssl')
    ;

    $p->addLibrary($lib);

};
