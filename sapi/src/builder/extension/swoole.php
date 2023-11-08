<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $swoole_tag = 'v5.1.0';
    $file = "swoole-{$swoole_tag}.tar.gz";
    $url = "https://github.com/swoole/swoole-src/archive/refs/tags/{$swoole_tag}.tar.gz";

    $dependent_libraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'unix_odbc', 'pgsql'];
    $dependent_extensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];
    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-swoole-coro-time --enable-thread-context ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;
    $options .= ' --enable-swoole-sqlite --enable-swoole-pgsql ';
    $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';


    $ext = (new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/');
    call_user_func_array([$ext, 'withDependentLibraries'], $dependent_libraries);
    call_user_func_array([$ext, 'withDependentExtensions'], $dependent_extensions);
    $p->addExtension($ext);
};
