<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('exif'))
            ->withHomePage('https://www.php.net/exif')
            ->withOptions('--enable-exif')
    );
};
