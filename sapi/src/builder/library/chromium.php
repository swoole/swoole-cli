<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lib = new Library('chromium');
    $lib->withHomePage('https://chromium.googlesource.com/chromium/src')
        ->withLicense('https://https://chromium.googlesource.com/chromium/src/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/chromium/src')
        ->withDownloadScript(
            'chromium',
            <<<EOF
               git clone -b main --depth=1  https://chromium.googlesource.com/chromium/src  chromium
EOF
        )
        ->withBuildLibraryCached(false)
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)

        ->withBuildScript(
            <<<EOF
            mkdir -p build
             cd build

EOF
        )
        ->withDependentLibraries('depot_tools')
    ;

    $p->addLibrary($lib);
};
