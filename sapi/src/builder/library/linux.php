<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lib = new Library('linux');
    $lib->withHomePage('https://www.kernel.org/')
        ->withLicense('https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git/tree/LICENSES', Library::LICENSE_GPL)
        ->withManual('https://git.kernel.org/')
        ->withDownloadScript(
            'linux-kernel',
            <<<EOF
               git clone -b main --depth=1  git://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git linux-kernel
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

    ;

    $p->addLibrary($lib);
};
