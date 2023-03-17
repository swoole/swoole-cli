<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $option = '--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml';
    $p->addExtension(
        (new Extension('xml'))
            ->withOptions($option)
            ->depends('libxml2')
    );
};
