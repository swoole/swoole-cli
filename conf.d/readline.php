<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('readline'))
            ->withOptions('--with-readline=' . READLINE_PREFIX)
            ->depends('ncurses', 'readline')
    );
};
