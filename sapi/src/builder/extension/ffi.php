<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $php_version = BUILD_PHP_VERSION;
    $libffi_prefix = LIBFFI_PREFIX;
    $p->addExtension(
        (new Extension('ffi'))
            ->withHomePage('https://www.php.net/pgsql')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withOptions('--with-ffi=' . $libffi_prefix)
            ->withDependentLibraries('libffi')
    );
};
