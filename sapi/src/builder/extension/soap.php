<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('soap'))
            ->withHomePage('https://www.php.net/soap')
            ->withOptions('--enable-soap')
            ->withDependentLibraries('libxml2')
    );
};
