<?php

use SwooleCli\Preprocessor;
use SwooleCli\Library;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    if ($p->getOsType() == 'macos') {
        $bison_prefix = BISON_PREFIX;
        $p->addLibrary(
            (new Library('bison'))
                ->withHomePage('https://www.gnu.org/software/bison/')
                ->withUrl('http://ftp.gnu.org/gnu/bison/bison-3.8.tar.gz')
                ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
                ->withConfigure(
                    "
                    ./configure --help
                    ./configure --prefix={$bison_prefix}
                    "
                )
                ->withBinPath($bison_prefix . '/bin/')
        );
    }
    $p->withExportVariable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->withExportVariable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');
    $p->addExtension(
        (new Extension('mongodb'))
            ->withHomePage('https://www.php.net/mongodb')
            ->withHomePage('https://www.mongodb.com/docs/drivers/php/')
            ->withOptions('--enable-mongodb --with-mongodb-system-libs=no --with-mongodb-ssl=openssl')
            ->withPeclVersion('1.15.2')
            ->depends('icu', 'openssl', 'zlib', 'libzstd')
    );
};
