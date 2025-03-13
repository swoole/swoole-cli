<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {


    $options = [];

    $dependentLibraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'unix_odbc', 'pgsql', 'libzstd'];
    $dependentExtensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];

    // v5.1.x 不支持 PHP 8.4
    // swoole 支持计划 https://wiki.swoole.com/zh-cn/#/version/supported?id=%e6%94%af%e6%8c%81%e8%ae%a1%e5%88%92

    $swoole_tag = 'v6.0.1';
    $file = "swoole-{$swoole_tag}.tar.gz";

    $url = "https://github.com/swoole/swoole-src/archive/refs/tags/{$swoole_tag}.tar.gz";

    if ($p->getBuildType() === 'debug') {
        $options[] = ' --enable-debug ';
        $options[] = ' --enable-debug-log ';
        $options[] = ' --enable-swoole-coro-time  ';
    }

    //call_user_func_array([$ext, 'withDependentLibraries'], $dependentLibraries);
    //call_user_func_array([$ext, 'withDependentExtensions'], $dependentExtensions);

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
    $options[] = '--enable-zts';
    $options[] = '--disable-opcache-jit';

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
        ->withFile($file)
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
            git clone -b {$swoole_tag} --depth=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withOptions(implode(' ', $options))
        ->withBuildCached(false)
        ->withDependentLibraries(...$dependentLibraries)
        ->withDependentExtensions(...$dependentExtensions));

    $p->withVariable('LIBS', '$LIBS ' . ($p->isMacos() ? '-lc++' : '-lstdc++'));
    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');

    $p->withExportVariable('ZSTD_CFLAGS', '$(pkg-config  --cflags --static  libzstd)');
    $p->withExportVariable('ZSTD_LIBS', '$(pkg-config    --libs   --static  libzstd)');
};
