<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lib = new Library('confd');
    $lib->withHomePage('http://www.confd.io/')
        ->withLicense('https://github.com/kelseyhightower/confd/blob/master/LICENSE', Library::LICENSE_MIT)
        ->withManual('https://github.com/kelseyhightower/confd/blob/master/docs/installation.md')
        ->withManual('https://github.com/kelseyhightower/confd.git')

        ->withFile('confd-latest.tar.gz')
        ->withDownloadScript(
            'confd',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/kelseyhightower/confd.git
EOF
        )

        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildCached(false)
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            apt -y install golang
EOF
        )

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF

            make build
            ls bin/
            # make install
EOF
        )


        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')

    ;

    $p->addLibrary($lib);
};
