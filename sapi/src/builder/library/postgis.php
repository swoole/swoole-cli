<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lib = new Library('postgis');
    $lib->withHomePage('https://postgis.net')
        ->withLicense('https://git.osgeo.org/gitea/postgis/postgis/src/branch/master/LICENSE.TXT', Library::LICENSE_SPEC)
        ->withManual('https://postgis.net/development/source_code/')
        ->withManual('https://git.osgeo.org/gitea/postgis/postgis')
        ->withDownloadScript(
            'postgis',
            <<<EOF
               git clone -b main --depth=1 --single-branch  https://git.osgeo.org/gitea/postgis/postgis.git
EOF
        )
        ->withBuildCached(false)
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
