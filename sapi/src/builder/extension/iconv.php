<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('iconv'))
            ->withHomePage('https://www.php.net/iconv')
            ->withOptions('--with-iconv=' . ICONV_PREFIX)
            ->withDependentLibraries('libiconv')
    );
};
