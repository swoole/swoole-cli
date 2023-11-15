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
        ->withManual('https://docs.asterisk.org/About-the-Project/')
        ->withFile('asterisk-latest.tar.gz')
        ->withDownloadScript(
            'asterisk',
            <<<EOF
                git clone  --depth=1 https://github.com/asterisk/asterisk
EOF
        )
        ->withBuildScript(
            <<<EOF
        ./configure --help
EOF
        )
        ->withBinPath($asterisk_prefix . '/bin/')
        ->withDependentLibraries(
            // 'libpcap',
            //  'openssl',
            // 'zlib',
            //'libpri',
            'dahdi_linux',
            'dahdi_tools',
            //'dahdi_complete',
            // 'pjproject',
            //'libsrtp'
        );

    $p->addLibrary($lib);
};
