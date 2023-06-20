<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('ssh2'))
            ->withOptions('--with-ssh2=' . LIBSSH2_PREFIX)
            ->withPeclVersion('1.4')
            ->withHomePage('https://github.com/php/pecl-networking-ssh2')
            ->withManual('https://www.php.net/ssh2')
            ->withLicense('https://www.php.net/license/', Extension::LICENSE_PHP)
            ->withDependentLibraries('libssh2')
    );
};
