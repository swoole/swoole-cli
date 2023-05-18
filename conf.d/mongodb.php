<?php

use SwooleCli\Preprocessor;
use SwooleCli\Library;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    if (1 || $p->getOsType() == 'macos') {
        $bison_prefix = BISON_PREFIX;
        $p->addLibrary(
            (new Library('bison'))
                ->withHomePage('https://www.gnu.org/software/bison/')
                ->withUrl('http://ftp.gnu.org/gnu/bison/bison-3.8.tar.gz')
                ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
                ->withPrefix($bison_prefix)
                ->withConfigure(
                    "
                    ./configure --help
                    ./configure --prefix={$bison_prefix}
                    "
                )
                ->withBinPath($bison_prefix . '/bin/')
        );
    }
    if (0) {
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
                -DCMAKE_INSTALL_PREFIX={$snappy_prefix} \
                -DCMAKE_INSTALL_LIBDIR={$snappy_prefix}/lib \
                -DCMAKE_INSTALL_INCLUDEDIR={$snappy_prefix}/include \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON \
                -DSNAPPY_BUILD_TESTS=OFF \
                -DSNAPPY_BUILD_BENCHMARKS=OFF \

EOF
                )
                ->withPkgName('snappy')
                ->withBinPath($snappy_prefix . '/bin/')
        );

        $libsasl_prefix = LIBSASL_PREFIX;
        $p->addLibrary(
            (new Library('libsasl'))
                ->withHomePage('https://www.cyrusimap.org/sasl/')
                ->withManual('https://www.cyrusimap.org/sasl/sasl/installation.html#installation')
                ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
                ->withUrl(
                    'https://github.com/cyrusimap/cyrus-sasl/releases/download/cyrus-sasl-2.1.28/cyrus-sasl-2.1.28.tar.gz'
                )
                ->withFile('cyrus-sasl-2.1.28.tar.gz')
                ->withPrefix($libsasl_prefix)
                ->withConfigure(
                    <<<EOF

                ./configure --help
                # 支持很多参数，按需要启用
                ./configure \
                --prefix={$libsasl_prefix} \
                 --enable-static=yes \
                 --enable-shared=no \


EOF
                )
                ->withPkgName('libsasl2')
                ->withBinPath($libsasl_prefix . '/sbin/')
        );
    }
    $p->withExportVariable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->withExportVariable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');
    $options = ' --enable-mongodb --with-mongodb-system-libs=no --with-mongodb-ssl=openssl  ';


    $p->addExtension(
        (new Extension('mongodb'))
            ->withHomePage('https://www.php.net/mongodb')
            ->withHomePage('https://www.mongodb.com/docs/drivers/php/')
            ->withPeclVersion('1.15.3')
            ->withOptions(
                $options
            )
            ->depends('icu', 'openssl', 'zlib', 'libzstd')
    );
};
