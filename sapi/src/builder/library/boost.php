<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $boost_prefix = BOOST_PREFIX;
    $lib = new Library('boost');
    $lib->withHomePage('https://www.boost.org/')
        ->withLicense('https://www.boost.org/users/license.html', Library::LICENSE_SPEC)
        ->withUrl('https://boostorg.jfrog.io/artifactory/main/release/1.81.0/source/boost_1_81_0.tar.gz')
        ->withManual('https://www.boost.org/doc/libs/1_81_0/more/getting_started/index.html')
        ->withManual('https://github.com/boostorg/wiki/wiki/')
        ->withManual('https://github.com/boostorg/wiki/wiki/Getting-Started%3A-Overview')
        ->withManual('https://www.boost.org/build/')
        ->withManual('https://www.boost.org/build/doc/html/index.html')
        ->withPrefix($boost_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($boost_prefix)
        ->withBuildScript(
            <<<EOF
            export PATH=\$SYSTEM_ORIGIN_PATH
            export PKG_CONFIG_PATH=\$SYSTEM_ORIGIN_PKG_CONFIG_PATH
            export Boost_USE_STATIC_LIBS=on
            ./bootstrap.sh
            ./b2 headers
            ./b2 --release install --prefix={$boost_prefix}

            export PATH=\$SWOOLE_CLI_PATH
            export PKG_CONFIG_PATH=\$SWOOLE_CLI_PKG_CONFIG_PATH
EOF
        )
        ->withPkgName('boost');

    $p->addLibrary($lib);
};
