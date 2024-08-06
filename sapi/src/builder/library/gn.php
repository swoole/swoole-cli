<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $depot_tools_prefix = DEPOT_TOOLS_PREFIX;
    $gn_prefix = GN_PREFIX;
    $lib = new Library('gn'); //gn 包含在 depot_tools 项目里
    $lib->withHomePage('https://gn.googlesource.com/gn')
        ->withLicense(
            'https://gn.googlesource.com/gn/+/refs/heads/main/LICENSE',
            Library::LICENSE_SPEC
        )
        ->withManual('https://gn.googlesource.com/gn')
        ->withManual(
            'https://gn.googlesource.com/gn'
        )
        ->withFile('gn-latest.tar.gz')
        ->withDownloadScript(
            'depot_tools',
            <<<EOF
                git clone -b main  --single-branch  --depth=1 https://gn.googlesource.com/gn
EOF
        )
        ->withPrefix($gn_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
            mkdir -p {$gn_prefix}
            cd ..
            cp -rf gn/* {$gn_prefix}



EOF
        )
        ->withBinPath($depot_tools_prefix . '/bin/')
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
