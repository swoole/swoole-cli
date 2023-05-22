<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('fileinfo'))
            ->withHomePage('https://www.php.net/fileinfo')
            ->withOptions('--enable-fileinfo')
    );
};
