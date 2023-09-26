<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $options = '--with-pdo-sqlite';
    if ($php_version_id < 7040) {
        $options = '--with-pdo-sqlite=' . SQLITE3_PREFIX;
    }

    $p->addExtension(
        (new Extension('pdo_sqlite'))
            ->withHomePage('https://www.php.net/pdo_sqlite')
            ->withOptions($options)
            ->withDependentLibraries('sqlite3')
            ->withDependentExtensions('pdo')
    );
};
