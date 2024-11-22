<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $swoole_tag = 'v5.1.5';
    $file = "swoole-{$swoole_tag}.tar.gz";

    $url = "https://github.com/swoole/swoole-src/archive/refs/tags/{$swoole_tag}.tar.gz";

    $options = [];

    if ($p->getBuildType() === 'debug') {
        $options[] = ' --enable-debug ';
        $options[] = ' --enable-debug-log ';
        $options[] = ' --enable-swoole-coro-time  ';
    }

    //call_user_func_array([$ext, 'withDependentLibraries'], $dependentLibraries);
    //call_user_func_array([$ext, 'withDependentExtensions'], $dependentExtensions);

    $dependentLibraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'unix_odbc', 'pgsql'];
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


    // 扩展钩子
    $p->withBeforeConfigureScript('swoole', function (Preprocessor $p) {
        $workDir = $p->getPhpSrcDir();
        $cmd = <<<EOF
        cd {$workDir}
        sed -i.backup "s/php_strtolower(/zend_str_tolower(/" ext/swoole/ext-src/swoole_redis_server.cc
EOF;

        if (BUILD_CUSTOM_PHP_VERSION_ID >= 8040) {
            //参考
            //https://github.com/swoole/swoole-src/blob/4787a8a0e8b4adb0e8643901d2b5bae4fafe0876/ext-src/swoole_redis_server.cc#L162
            $cmd .= PHP_EOL;
        } else {
            $cmd = '';
        }
        return $cmd;
    });
};
