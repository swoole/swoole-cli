<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dahdi_linux_prefix = DAHDI_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('dahdi_linux');
    $lib->withHomePage('https://www.asterisk.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/asterisk/dahdi-linux.git')
        ->withFile('dahdi-linux-latest.tar.gz')
        ->withDownloadScript(
            'dahdi-linux',
            <<<EOF
                git clone  --depth=1 https://github.com/asterisk/dahdi-linux.git
EOF
        )
        ->withBuildLibraryCached(false)
        ->withPrefix($dahdi_linux_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($dahdi_linux_prefix)
        ->withBuildScript(
            <<<EOF

            PACKAGES='zlib  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            make -j {$p->maxJob}  DESTDIR={$dahdi_linux_prefix}
EOF
        )
    ;

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $openssl_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $openssl_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lssl ');
};
