<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gettext'))
            ->withHomePage('https://www.php.net/gettext')
            ->withOptions('--with-gettext=' . GETTEXT_PREFIX)
            ->withDependentLibraries('gettext')
    );
};
