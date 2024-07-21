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
            ->withFile('ffi-' . $php_version . '.tgz')
            ->withBuildCached(false)
            ->withAutoUpdateFile()
            ->withDownloadScript(
                'ffi',
                <<<EOF
                git clone -b php-{$php_version} --depth=1 https://github.com/php/php-src.git
                cd php-src/ext/


EOF
            )
            ->withDependentLibraries('libffi')
    );
};
