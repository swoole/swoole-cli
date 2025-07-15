<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $dependentLibraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'unix_odbc', 'pgsql', 'libzstd'];
    $dependentExtensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];

    $options[] = '--enable-swoole';
    $options[] = '--enable-sockets';
    $options[] = '--enable-mysqlnd';
    $options[] = '--enable-swoole-curl';
    $options[] = '--enable-cares';
    $options[] = '--with-brotli-dir=' . BROTLI_PREFIX;
    $options[] = '--with-nghttp2-dir=' . NGHTTP2_PREFIX;
    $options[] = '--enable-swoole-pgsql';
    $options[] = '--enable-swoole-sqlite';
    $options[] = '--with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX;
    $options[] = '--enable-swoole-thread';
    $options[] = '--enable-brotli';
    $options[] = '--enable-zstd';

    if ($p->isLinux() && $p->getInputOption('with-iouring')) {
        $options[] = '--enable-iouring';
        $dependentLibraries[] = 'liburing';
        $p->withExportVariable('URING_CFLAGS', '$(pkg-config  --cflags --static  liburing)');
        $p->withExportVariable('URING_LIBS', '$(pkg-config    --libs   --static  liburing)');
    }

    $p->addExtension((new Extension('swoole'))
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions(implode(' ', $options))
        ->withBuildCached(false)
        ->withDependentLibraries(...$dependentLibraries)
        ->withDependentExtensions(...$dependentExtensions));

    $p->withVariable('LIBS', '$LIBS ' . ($p->isMacos() ? '-lc++' : '-lstdc++'));
    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');
    $p->withExportVariable('ZSTD_CFLAGS', '$(pkg-config  --cflags --static  libzstd)');
    $p->withExportVariable('ZSTD_LIBS', '$(pkg-config    --libs   --static  libzstd)');

    $p->withBeforeConfigureScript('swoole', function () use ($p) {
        $workDir = $p->getWorkDir();
        $shell = "set -x ;cd {$workDir} ; WORKDIR={$workDir} ;" . PHP_EOL;
        $shell .= <<<'EOF'

        SWOOLE_VERSION=$(awk 'NR==1{ print $1 }' "sapi/SWOOLE-VERSION.conf")
        CURRENT_SWOOLE_VERSION=''

        if [ -f "ext/swoole/CMakeLists.txt" ] ;then
            CURRENT_SWOOLE_VERSION=$(grep 'set(SWOOLE_VERSION' ext/swoole/CMakeLists.txt | awk '{ print $2 }' | sed 's/)//')
            if [[ "${CURRENT_SWOOLE_VERSION}" =~ "-dev" ]]; then
                echo 'swoole version master'
            fi
        fi

        if [ "${SWOOLE_VERSION}" != "${CURRENT_SWOOLE_VERSION}" ] ;then
            test -d ext/swoole && rm -rf ext/swoole
            if [ ! -f ${WORKDIR}/pool/ext/swoole-${SWOOLE_VERSION}.tgz ] ;then
                test -d /tmp/swoole && rm -rf /tmp/swoole
                git clone -b "${SWOOLE_VERSION}" https://github.com/swoole/swoole-src.git /tmp/swoole
                cd  /tmp/swoole
                tar -czvf ${WORKDIR}/pool/ext/swoole-${SWOOLE_VERSION}.tgz .
            fi
            mkdir -p ${WORKDIR}/ext/swoole/
            tar --strip-components=1 -C ${WORKDIR}/ext/swoole/ -xf ${WORKDIR}/pool/ext/swoole-${SWOOLE_VERSION}.tgz
        fi
        # swoole extension hook
EOF;

        return $shell;
    });
};
