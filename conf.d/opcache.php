<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $option = '--enable-opcache';
    if($p->getOsType() == 'macos') {
        $option .= ' --disable-opcache-jit';
    }
    $p->addExtension((new Extension('opcache'))->withOptions($option));
};
