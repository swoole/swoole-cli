<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $snappy_prefix = SNAPPY_PREFIX;

    $p->withExportVariable('PHP_MONGODB_SSL_CFLAGS', '$(pkg-config --cflags --static libcrypto libssl  openssl)');
    $p->withExportVariable('PHP_MONGODB_SSL_LIBS', '$(pkg-config   --libs   --static libcrypto libssl  openssl)');

    $p->withExportVariable('PHP_MONGODB_ICU', 'yes');
    $p->withExportVariable('PHP_MONGODB_ICU_CFLAGS', '$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)');
    $p->withExportVariable('PHP_MONGODB_ICU_LIBS', '$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)');

    $p->withExportVariable('PHP_MONGODB_SNAPPY_CFLAGS', '-I' . $snappy_prefix . '/include');
    $p->withExportVariable('PHP_MONGODB_SNAPPY_LIBS', '-L' . $snappy_prefix . '/lib -lsnappy');

    $p->withExportVariable('PHP_MONGODB_ZSTD_CFLAGS', '$(pkg-config --cflags --static libzstd)');
    $p->withExportVariable('PHP_MONGODB_ZSTD_LIBS', '$(pkg-config   --libs   --static libzstd)');

    $p->withExportVariable('PHP_MONGODB_ZLIB_CFLAGS', '$(pkg-config --cflags --static zlib)');
    $p->withExportVariable('PHP_MONGODB_ZLIB_LIBS', '$(pkg-config   --libs   --static zlib)');

    if ($p->isMacos()) {
        throw new \RuntimeException("macos 暂不支持，等待改进");
    }
    # PHP 8.2 以上 使用clang 编译
    # 需要解决这个问题 https://github.com/mongodb/mongo-php-driver/issues/1445
    # fix PR https://github.com/mongodb/mongo-php-driver/releases/tag/1.16.2

    $options = ' --enable-mongodb ';
    $options .= ' --with-mongodb-system-libs=no ';
    $options .= ' --with-mongodb-client-side-encryption=no ';
    $options .= ' --with-mongodb-snappy=yes ';
    $options .= ' --with-mongodb-zlib=yes ';
    $options .= ' --with-mongodb-zstd=yes ';
    $options .= ' --with-mongodb-sasl=no ';
    $options .= ' --with-mongodb-ssl=openssl ';

    $mongodb_version = '1.19.4';
    $depends = ['icu', 'openssl', 'zlib', 'libzstd', 'snappy'];
    $ext = new Extension('mongodb');

    $ext->withHomePage('https://www.php.net/mongodb')
        ->withHomePage('https://www.mongodb.com/docs/drivers/php/')
        ->withOptions($options)
        ->withFile("mongodb-{$mongodb_version}.tgz")
        ->withDownloadScript(
            'mongo-php-driver',
            <<<EOF
        git clone -b {$mongodb_version} --depth=1 --recursive https://github.com/mongodb/mongo-php-driver.git

        # git clone -b {$mongodb_version} --depth=1  https://github.com/mongodb/mongo-php-driver.git
        # CURRENT_DIR=$(PWD)
        # cd mongo-php-driver/src/
        # rm -rf libmongoc
        # rm -rf libmongocrypt
        # git clone -b 1.26.2 --depth=1  https://github.com/mongodb/mongo-c-driver.git libmongoc
        # git clone -b 1.9.1  --depth=1 https://github.com/mongodb/libmongocrypt.git libmongocrypt
        # cd \$CURRENT_DIR

EOF
        )
        //->withAutoUpdateFile()
        ->withBuildCached(false)
        ->withDependentLibraries(...$depends);

    $p->addExtension($ext);

    $p->withBeforeConfigureScript('mongodb', function (Preprocessor $p) {
        $build_dir = $p->getBuildDir();
        $php_version = BUILD_PHP_VERSION;

        $php_version_id = BUILD_PHP_VERSION_ID;
        $php_version_id = substr($php_version_id, 0, 4) . (int)substr($php_version_id, 4);
        $php_version_id = (int)$php_version_id;

        return <<<EOF
            cd {$build_dir}/php-src/ext/mongodb/
            sed -i.bak "s/AC_MSG_ERROR(\[php-config not found\])/AC_MSG_RESULT(\[x-custom-php-config\])/"  config.m4
            sed -i.bak "s/PHP_MONGODB_PHP_VERSION=\`\\\${PHP_CONFIG} --version\`/PHP_MONGODB_PHP_VERSION=\"{$php_version}\"/"  config.m4
            sed -i.bak "s/PHP_MONGODB_PHP_VERSION_ID=\`\\\${PHP_CONFIG} --vernum\`/PHP_MONGODB_PHP_VERSION_ID={$php_version_id}/"  config.m4

EOF;
    });
};
