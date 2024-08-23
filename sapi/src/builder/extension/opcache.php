<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('opcache'))
            ->withHomePage('https://www.php.net/opcache')
            # zts 模式下， 静态编译 opcache-jit 报错 ，更多信息： https://github.com/php/php-src/issues/15074
            ->withOptions('--enable-opcache --disable-opcache-jit')
    );
};
