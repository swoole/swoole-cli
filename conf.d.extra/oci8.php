<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('oci8'))
            ->withHomePage('https://www.php.net/oci8')
            ->withManual('https://www.php.net/manual/zh/oci8.installation.php')
            ->withLicense('https://github.com/php/php-src/blob/master/ext/oci8/LICENSE', Extension::LICENSE_PHP)
            ->withOptions('--with-oci8=instantclient,/path/to/instant/client/lib')
            ->withPeclVersion('3.3.0')
        //->depends('Oracle Instant Client')
        // https://www.oracle.com/database/technologies/instant-client/downloads.html
    );
};
