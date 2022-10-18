<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=/usr/bzip2'));
};
