<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $php_version = BUILD_PHP_VERSION;
    $p->addExtension(
        (new Extension('pgsql'))
            ->withHomePage('https://www.php.net/pgsql')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src.git ')
            ->withPeclVersion($php_version)
            ->withFile('pgsql-' . $php_version . '.tgz')
            ->withDownloadScript(
                'pgsql',
                <<<EOF
                git clone -b php-{$php_version} --depth=1 https://github.com/php/php-src.git
                cp -rf php-src/ext/pgsql  pgsql
EOF
            )
            ->withOptions('--with-pgsql=' . PGSQL_PREFIX)->depends('pgsql')
    );
};
