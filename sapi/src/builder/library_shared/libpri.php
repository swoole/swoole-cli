<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $libpri_prefix = LIBPRI_PREFIX;

    // libpri：基本速率 ISDN 的实现

    $dahdi_tools_prefix = DAHDI_TOOLS_PREFIX;

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
        ->withBuildScript(
            <<<EOF

            CPPFLAGS=" I{$dahdi_tools_prefix}/include/dahdi/" \
            make -j {$p->maxJob}  DESTDIR={$libpri_prefix}


EOF
        )
        ->withBinPath($libpri_prefix . '/bin/')
        ->withDependentLibraries('dahdi_tools')
    ;

    $p->addLibrary($lib);
};
