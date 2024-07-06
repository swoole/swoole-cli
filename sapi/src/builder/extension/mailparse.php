<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('mailparse'))
            ->withHomePage('https://pecl.php.net/package/mailparse')
            ->withLicense('https://github.com/php/pecl-mail-mailparse/blob/v3.1.6/LICENSE', Extension::LICENSE_BSD)
            ->withManual('https://github.com/php/pecl-mail-mailparse.git')
            ->withPeclVersion('3.1.6')
            ->withOptions(' --enable-mailparse ')
            ->withDependentExtensions('mbstring')
    );
};
