<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    //$snappy_prefix = SNAPPY_PREFIX;

    $p->withExportVariable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');

    $p->withExportVariable('PHP_MONGODB_ICU', 'yes');
    $p->withExportVariable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->withExportVariable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');

    //$p->withExportVariable('PHP_MONGODB_SNAPPY_CFLAGS', '-I' . $snappy_prefix . '/include');
    //$p->withExportVariable('PHP_MONGODB_SNAPPY_LIBS', '-L' . $snappy_prefix . '/lib -lsnappy');

    $p->withExportVariable('PHP_MONGODB_ZSTD_CFLAGS', '$(pkg-config --cflags --static libzstd)');
    $p->withExportVariable('PHP_MONGODB_ZSTD_LIBS', '$(pkg-config   --libs   --static libzstd)');

    $p->withExportVariable('PHP_MONGODB_ZLIB_CFLAGS', '$(pkg-config --cflags --static zlib)');
    $p->withExportVariable('PHP_MONGODB_ZLIB_LIBS', '$(pkg-config   --libs   --static zlib)');

    $mongodb_version = '1.19.4';

    $options = [];
    $options[] = ' --enable-mongodb ';
    $options[] = ' --with-mongodb-system-libs=no ';
    $options[] = ' --with-mongodb-client-side-encryption=no ';
    $options[] = ' --with-mongodb-ssl=openssl ';
    $options[] = ' --with-mongodb-snappy=no ';
    $options[] = ' --with-mongodb-zlib=yes ';
    $options[] = ' --with-mongodb-zstd=yes ';
    $options[] = ' --with-mongodb-sasl=no ';
    $options[] = ' --enable-mongodb-crypto-system-profile=no ';
    $options[] = ' --with-mongodb-utf8proc=bundled ';
    $options[] = ' --with-openssl-dir=' . OPENSSL_PREFIX;


    $dependentLibraries = ['icu', 'openssl', 'zlib', 'libzstd'];

    //$dependentLibraries[] = 'libsasl';
    //$dependentLibraries[] = 'snappy';
    $ext = new Extension('mongodb');

    $ext->withManual('https://www.php.net/mongodb')
        ->withHomePage('https://www.mongodb.com/docs/drivers/php/')
        ->withOptions(implode(' ', $options))
        //->withPeclVersion('1.19.4')
        //->withFileHash('md5', '91f96b24df7ed5651731671f55cb68a1')
        ->withFile("mongodb-{$mongodb_version}.tgz")
        ->withDownloadScript(
            'mongo-php-driver',
            <<<EOF
        git clone -b {$mongodb_version} --depth=1 --recursive https://github.com/mongodb/mongo-php-driver.git
EOF
        )
        //->withAutoUpdateFile()
        ->withBuildCached(false)
        ->withDependentLibraries(...$dependentLibraries);
    $p->addExtension($ext);
    $p->withVariable('LIBS', '$LIBS ' . ($p->isMacos() ? '-lc++' : '-lstdc++'));
};
