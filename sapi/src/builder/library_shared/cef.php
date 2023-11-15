<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('cef');
    $lib->withHomePage('https://bitbucket.org/chromiumembedded/cef/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/chromiumembedded/cef.git')
        ->withFile('cef-latest.tar.gz')
        ->withDownloadScript(
            'cef',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/chromiumembedded/cef.git
EOF
        )
        ->withPrefix($example_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

EOF
        )
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl', 'depot_tools')
    ;

    $p->addLibrary($lib);
};
