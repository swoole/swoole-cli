<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('libmysqlclient');
    $lib->withHomePage('https://dev.mysql.com/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://dev.mysql.com/doc/c-api/5.7/en/c-api-building-clients.html')
    ;

    $p->addLibrary($lib);
};
