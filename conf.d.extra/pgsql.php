<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $pgsql_version=$p::SWOOLE_CLI_PHP_VERSION;
    $p->addExtension(
        (new Extension('pgsql'))
            ->withHomePage('https://www.php.net/pdo_pgsql')
            ->withPeclVersion( $pgsql_version)
            ->withDownloadScript(
                "pgsql",
                <<<EOF
                test -d php-src && rm -rf php-src
                git clone -b php-{$pgsql_version} --depth=1 https://github.com/php/php-src.git
                cp -rf php-src/ext/pgsql pgsql
EOF
            )
            ->withOptions('--with-pgsql=' . PGSQL_PREFIX)
            ->depends('pgsql')
    );
};
