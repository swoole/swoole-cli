<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    return null;//待改进
    $depends = ['libgnupg'];
    $options = '--with-gnupg' . EXAMPLE_PREFIX;

    $ext = (new Extension('gnupg'))
        ->withLicense('https://github.com/php-gnupg/php-gnupg/blob/master/LICENSE', Extension::LICENSE_PHP)
        ->withHomePage('https://pecl.php.net/package/gnupg')
        ->withManual('https://github.com/php-gnupg/php-gnupg.git')
        ->withOptions($options)
        ->withPeclVersion('1.5.1')
        ->withDependentExtensions(...$depends);
    $p->addExtension($ext);
};
