<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $php_version = BUILD_PHP_VERSION;
    $p->addExtension(
        (new Extension('pdo_pgsql'))
            ->withHomePage('https://www.php.net/pdo_pgsql')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src.git ')
            ->withPeclVersion($php_version)
            ->withFile('pdo_pgsql-' . $php_version . '.tgz')
            ->withDownloadScript(
                'pdo_pgsql',
                <<<EOF
                git clone -b php-{$php_version} --depth=1 https://github.com/php/php-src.git
                cp -rf php-src/ext/pdo_pgsql  pdo_pgsql
EOF
            )
            ->withOptions('--with-pdo-pgsql=' . PGSQL_PREFIX)->depends('pgsql')
    );
};
