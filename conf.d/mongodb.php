<?php

use SwooleCli\Preprocessor;
use SwooleCli\Library;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->withExportVariable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');
    $options = ' --enable-mongodb --with-mongodb-system-libs=no --with-mongodb-ssl=openssl  ';
    $ext = new Extension('mongodb');

    $ext->withHomePage('https://www.php.net/mongodb')
        ->withHomePage('https://www.mongodb.com/docs/drivers/php/')
        ->withOptions($options)
        ->withPeclVersion('1.14.2')
        ->depends('icu', 'openssl', 'zlib', 'libzstd');
    if ($p->getOsType() == 'macos') {
        $ext->depends('bison');
    }
    $p->addExtension($ext);
};
