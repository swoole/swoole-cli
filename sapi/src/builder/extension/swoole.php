<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
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
    $options[] = '--enable-swoole-stdext';

    if ($p->isLinux() && $p->getInputOption('with-iouring')) {
        $options[] = '--enable-iouring';
        $dependentLibraries[] = 'liburing';
        $p->withExportVariable('URING_CFLAGS', '$(pkg-config  --cflags --static  liburing)');
        $p->withExportVariable('URING_LIBS', '$(pkg-config    --libs   --static  liburing)');
    }
    $swoole_version = trim(file_get_contents(__DIR__ . '/../../../SWOOLE-VERSION.conf'));
    $p->addExtension(
        (new Extension('swoole'))
            ->withHomePage('https://github.com/swoole/swoole-src')
            ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://wiki.swoole.com/#/')
            ->withOptions(implode(' ', $options))
            ->withBuildCached(false)
            ->withDependentLibraries(...$dependentLibraries)
            ->withDependentExtensions(...$dependentExtensions)
            ->withPieName('swoole/swoole')
            ->withPieVersion($swoole_version)
    );

    $p->withVariable('LIBS', '$LIBS ' . ($p->isMacos() ? '-lc++' : '-lstdc++'));
    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');
    $p->withExportVariable('ZSTD_CFLAGS', '$(pkg-config  --cflags --static  libzstd)');
    $p->withExportVariable('ZSTD_LIBS', '$(pkg-config    --libs   --static  libzstd)');

    $p->withExportVariable('SWOOLE_ODBC_LIBS', '$(pkg-config    --libs-only-L --libs-only-l   --static  odbc odbccr odbcinst readline ncursesw ) ' . " -L{$libiconv_prefix}/lib -liconv ");
};
