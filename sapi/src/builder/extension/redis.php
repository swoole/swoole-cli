<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $pecl_version = '5.3.7';
    if ($php_version_id < 7040) {
        $pecl_version = '4.2.0';
    }

    $p->addExtension(
        (new Extension('redis'))
            ->withOptions('--enable-redis')
            ->withPeclVersion($pecl_version)
            ->withHomePage('https://github.com/phpredis/phpredis')
            ->withLicense('https://github.com/phpredis/phpredis/blob/develop/COPYING', Extension::LICENSE_PHP)
            ->withDependentExtensions('session')
    );
};
