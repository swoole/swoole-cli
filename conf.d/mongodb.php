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
    $snappy_prefix = SNAPPY_PREFIX;
    $p->addLibrary(
        (new Library('snappy'))
            ->withHomePage('https://github.com/google/snappy')
            ->withManual('https://github.com/google/snappy/blob/main/README.md')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://github.com/google/snappy/archive/refs/tags/1.1.10.tar.gz')
            ->withFile('snappy-1.1.10.tar.gz')
            ->withPrefix($snappy_prefix)
            ->withConfigure(
                <<<EOF

                mkdir -p build
                cd build
                cmake .. \
                -Werror -Wsign-compare \
                -DCMAKE_INSTALL_PREFIX={$snappy_prefix} \
                -DCMAKE_INSTALL_LIBDIR={$snappy_prefix}/lib \
                -DCMAKE_INSTALL_INCLUDEDIR={$snappy_prefix}/include \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON

EOF
            )
            ->withPkgName('snappy')
            ->withBinPath($snappy_prefix . '/bin/')
    );
    $p->withExportVariable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->withExportVariable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');
    $options =' --enable-mongodb --with-mongodb-system-libs=no --with-mongodb-ssl=openssl  ';
    $options .=' --with-mongodb-sasl=no';

    $p->addExtension(
        (new Extension('mongodb'))
            ->withHomePage('https://www.php.net/mongodb')
            ->withHomePage('https://www.mongodb.com/docs/drivers/php/')
            ->withOptions(
                $options
            )
            ->withPeclVersion('1.14.2')
            ->depends('icu', 'openssl', 'zlib', 'libzstd', 'snappy')
    );
};
