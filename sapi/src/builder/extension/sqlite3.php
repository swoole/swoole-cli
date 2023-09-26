<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $options = '--with-sqlite3';
    if ($php_version_id < 7040) {
        $options = '--with-pdo-sqlite=' . SQLITE3_PREFIX;
    }

    $p->addExtension(
        (new Extension('sqlite3'))
            ->withHomePage(' https://www.php.net/sqlite3')
            ->withOptions($options)
            ->withDependentLibraries('sqlite3')
    );
};
