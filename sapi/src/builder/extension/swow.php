<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $swow_tag = 'v1.6.2';
    $options = ' --enable-swow ';
    $options .= ' --enable-swow-ssl ';
    $options .= ' --enable-swow-curl ';
    $options .= ' --enable-swow-pdo-pgsql=yes ';
    # $options .= ' --enable-swow-thread-context ';

    $dependentExtensions = ['curl', 'openssl', 'sockets', 'pdo'];
    $dependentLibraries = ['openssl', 'pgsql', 'curl'];

    $p->addExtension(
        (new Extension('swow'))
            ->withOptions($options)
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://docs.toast.run/swow/en/install.html')
            ->withFile("swow-{$swow_tag}.tar.gz")
            ->withDownloadScript(
                "swow",
                <<<EOF
                git clone -b {$swow_tag} https://github.com/swow/swow.git swow-code
                mv swow-code/ext  swow
EOF
            )
            ->withBuildCached(false)
            ->withDependentLibraries(...$dependentLibraries)
            ->withDependentExtensions(...$dependentExtensions)
    );
    $p->withExportVariable('POSTGRESQL_CFLAGS', '$(pkg-config  --cflags --static libpq)');
    $p->withExportVariable('POSTGRESQL_LIBS', '$(pkg-config    --libs   --static libpq)');

};
