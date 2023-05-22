<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('tokenizer'))
            ->withHomePage('https://www.php.net/tokenizer')
            ->withOptions('--enable-tokenizer')
    );
};
