<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('zlib'))
            ->withHomePage('https://www.php.net/zlib')
            ->withOptions('--with-zlib=' . ZLIB_PREFIX)
            ->withDependentLibraries('zlib')
    );
};
