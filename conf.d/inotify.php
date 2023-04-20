<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    if ($p->getOsType() != 'linux') {
        throw new \RuntimeException("Only supports linux");
    }
    $p->addExtension(
        (new Extension('inotify'))
            ->withHomePage('https://www.php.net/inotify')
            ->withOptions('--enable-inotify')
            ->withPeclVersion('3.0.0')
    );
};
