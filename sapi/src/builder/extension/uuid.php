<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    // uuid 扩展 依赖 libuuid 库 libintl 库

    // libuuid  库 存在于 Util-linux  (util_linux.php)

    $depends = ['util_linux'];
    $options = '--with-uuid=' . UTIL_LINUX_PREFIX;

    $ext = (new Extension('uuid'))
        ->withLicense('https://github.com/php/pecl-networking-uuid#LGPL-2.1-1-ov-file', Extension::LICENSE_LGPL)
        ->withHomePage('https://pecl.php.net/package/uuid')
        ->withManual('https://github.com/php/pecl-networking-uuid.git')
        ->withOptions($options)
        ->withPeclVersion('1.2.0')
        ->withDependentLibraries(...$depends);
    $p->addExtension($ext);
};
