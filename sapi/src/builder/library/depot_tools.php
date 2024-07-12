<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $depot_tools_prefix = DEPOT_TOOLS_PREFIX;

    //depot_tools 包含 gn
    //gn  https://gn.googlesource.com/gn

    $lib = new Library('depot_tools');
    $lib->withHomePage('https://chromium.googlesource.com/chromium/tools/depot_tools')
        ->withLicense(
            'https://chromium.googlesource.com/chromium/tools/depot_tools.git/+/refs/heads/main/LICENSE',
            Library::LICENSE_SPEC
        )
        ->withManual('https://gn.googlesource.com/gn')
        ->withManual(
            'https://commondatastorage.googleapis.com/chrome-infra-docs/flat/depot_tools/docs/html/depot_tools_tutorial.html#_setting_up'
        )
        ->withFile('depot_tools-latest.tar.gz')
        ->withDownloadScript(
            'depot-tools-latest',
            <<<EOF
            git clone  -b main --single-branch --depth=1  https://chromium.googlesource.com/chromium/tools/depot_tools
            mkdir depot-tools-latest
            mv depot_tools depot-tools-latest
EOF
        )
        ->withPrefix($depot_tools_prefix)
        ->withBuildCached(false)
        //->withAutoUpdateFile()
        ->withBuildScript(
            <<<EOF
           test -d {$depot_tools_prefix} && rm -rf {$depot_tools_prefix}

            mv depot_tools {$p->getGlobalPrefix()}
            # 禁止 DEPOT_TOOLS 自动更新
            export DEPOT_TOOLS_UPDATE=0

EOF
        )
        ->withBinPath($depot_tools_prefix)
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
