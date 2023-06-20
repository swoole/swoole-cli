<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('xml'))
            ->withHomePage('https://www.php.net/xml')
            ->withOptions(
                '--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml'
            )
            ->withDependentLibraries('libxml2')
    );
};
