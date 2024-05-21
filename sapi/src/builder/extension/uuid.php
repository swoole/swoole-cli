<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['libuuid', 'libintl'];
    $options = '--with-uuid=' . LIBUUID_PREFIX;

    $ext = (new Extension('uuid'))
        ->withLicense('https://github.com/php/pecl-networking-uuid#LGPL-2.1-1-ov-file', Extension::LICENSE_LGPL)
        ->withHomePage('https://pecl.php.net/package/uuid')
        ->withManual('https://github.com/php/pecl-networking-uuid.git')
        ->withOptions($options)
        ->withPeclVersion('1.2.0')
        ->withDependentLibraries(...$depends);
    $p->addExtension($ext);
};
