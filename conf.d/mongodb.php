<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setVarable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->setVarable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');
    $p->setVarable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->setVarable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');
    $p->addExtension(
        (new Extension('mongodb'))
            ->withOptions('--enable-mongodb --with-mongodb-system-libs=no --with-mongodb-ssl=openssl')
            ->withPeclVersion('1.14.2')
            ->depends('icu', 'openssl', 'zlib', 'libzstd')
    );
};
