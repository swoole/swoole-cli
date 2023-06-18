<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gmp'))
            ->withHomePage('https://www.php.net/gmp')
            ->withOptions('--with-gmp=' . GMP_PREFIX)->withDependentLibraries('gmp')
    );
};
