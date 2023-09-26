<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $options='--with-curl';
    if ($php_version_id < 7040) {
        $options= '--with-curl=' . CURL_PREFIX;
    }

    $p->addExtension(
        (new Extension('curl'))
            ->withHomePage('https://www.php.net/curl')
            ->withOptions($options)
            ->withDependentLibraries('curl')
    );
};
