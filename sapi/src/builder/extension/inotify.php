<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    if (!$p->isLinux()) {
        throw new \RuntimeException("Only supports linux");
    }
    $p->addExtension(
        (new Extension('inotify'))
            ->withHomePage('https://www.php.net/inotify')
            ->withOptions('--enable-inotify')
            ->withPeclVersion('3.0.0')
            ->withFileHash('md5', '084a5a5af53a5eb85dae7c7d2c95048f')
    );
};
