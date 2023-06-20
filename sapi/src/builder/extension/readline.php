<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('readline'))
            ->withHomePage('https://www.php.net/readline')
            ->withOptions('--with-readline=' . READLINE_PREFIX)
            ->withDependentLibraries('ncurses', 'readline')
    );
};
