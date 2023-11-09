<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $dependent_libraries = ['curl', 'openssl', 'cares', 'zlib'];

    $dependent_extensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';


    if ($php_version_id >= 8010) {
        $swoole_tag = 'v5.1.0';

        $dependent_libraries = array_merge($dependent_libraries, [
            'brotli',
            'nghttp2',
            'sqlite3',
            'unix_odbc',
            'pgsql'
        ]);
        $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
        $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;
        $options .= ' --enable-swoole-pgsql';
        $options .= ' --enable-swoole-sqlite';
        $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';
        $options .= ' --enable-swoole-coro-time --enable-thread-context ';
    } else {
        $swoole_tag = '4.8.x';
        if ($php_version_id < 8000) {
            $options .= ' --enable-http2 ';
            $options .= ' --enable-swoole-json ';
        }
        $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    }

    $file = "swoole-{$swoole_tag}.tar.gz";
    $url = "https://github.com/swoole/swoole-src/archive/refs/tags/{$swoole_tag}.tar.gz";


    $ext = (new Extension('swoole'))
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withFile($file)
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
            git clone -b {$swoole_tag} --depth=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withBuildCached(false)
    ;

    call_user_func_array([$ext, 'withDependentLibraries'], $dependent_libraries);
    call_user_func_array([$ext, 'withDependentExtensions'], $dependent_extensions);

    $p->addExtension($ext);
};
