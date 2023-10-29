<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $libpri_prefix = LIBPRI_PREFIX;

    // libpri：基本速率 ISDN 的实现

    $lib = new Library('libpri');
    $lib->withHomePage('https://www.asterisk.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/asterisk/libpri')
        ->withFile('libpri-latest.tar.gz')
        ->withDownloadScript(
            'libpri',
            <<<EOF
                git clone  --depth=1 https://github.com/asterisk/libpri
EOF
        )
        ->withBuildCached(false)
        ->withPrefix($libpri_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libpri_prefix)
        ->withBuildScript(
            <<<EOF


            PACKAGES='zlib  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            make -j {$p->maxJob}  DESTDIR={$libpri_prefix}


EOF
        )
        ->withPkgName('ssl')
        ->withBinPath($libpri_prefix . '/bin/')
        ->withDependentLibraries('zlib') //'dahdi_linux'
    ;

    $p->addLibrary($lib);
};
