<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $asterisk_prefix = ASTERISK_PREFIX;
    $lib = new Library('asterisk');
    $lib->withHomePage('https://www.asterisk.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/asterisk/asterisk')
        ->withManual('https://github.com/asterisk/dahdi-linux/wiki')
        ->withFile('asterisk-latest.tar.gz')
        ->withDownloadScript(
            'asterisk',
            <<<EOF
                git clone -b master --depth=1 https://github.com/asterisk/asterisk
EOF
        )
        ->withBuildLibraryCached(false)
        ->withPrefix($asterisk_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($asterisk_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"
            PACKAGES="\$PACKAGES libsrtp2"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$asterisk_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )

        ->withBinPath($asterisk_prefix . '/bin/')
        ->withDependentLibraries(
            'libpcap',
            'openssl',
            'zlib',
            'libpri',
            'dahdi_linux',
            'dahdi_tools',
            'dahdi_complete',
            'pjproject',
            'libsrtp'
        )

    ;

    $p->addLibrary($lib);
};
