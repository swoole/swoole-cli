<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $options = ' --enable-swow ';
    $options .= ' --enable-swow-ssl ';
    $options .= ' --enable-swow-curl ';
    $options .= ' --enable-swow-pdo-pgsql=yes ';
    # $options .= ' --enable-swow-thread-context ';

    $dependentExtensions = ['curl', 'openssl', 'sockets', 'pdo'];
    $dependentLibraries = ['openssl', 'pgsql', 'curl'];

    $p->addExtension(
        (new Extension('swow_latest'))
            ->withAliasName('swow')
            ->withOptions($options)
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://docs.toast.run/swow/en/install.html')
            ->withFile('swow-latest.tar.gz')
            ->withAutoUpdateFile()
            ->withDownloadScript(
                "swow",
                <<<EOF
                git clone -b develop https://github.com/swow/swow.git swow-code
                mv swow-code/ext  swow
EOF
            )
            ->withDependentLibraries(...$dependentLibraries)
            ->withDependentExtensions(...$dependentExtensions)
    );
    $p->withExportVariable('POSTGRESQL_CFLAGS', '$(pkg-config  --cflags --static libpq)');
    $p->withExportVariable('POSTGRESQL_LIBS', '$(pkg-config    --libs   --static libpq)');

};
