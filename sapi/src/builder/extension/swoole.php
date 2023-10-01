<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $dependent_libraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'unix_odbc'];
    $dependent_extensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-swoole-coro-time --enable-thread-context ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;
    $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';

    if ($p->getInputOption('with-swoole-pgsql')) {
        $options .= ' --enable-swoole-pgsql ';
        $dependent_libraries[] = 'pgsql';
    }

    $ext = (new Extension('swoole'))
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withManual('https://wiki.swoole.com/#/')
        ->withUrl('https://github.com/swoole/swoole-src/archive/refs/tags/v5.1.0.tar.gz')
        ->withFile('swoole-v5.1.0.tar.gz')
        ->withBuildLibraryCached(false);
    call_user_func_array([$ext, 'withDependentLibraries'], $dependent_libraries);
    call_user_func_array([$ext, 'withDependentExtensions'], $dependent_extensions);
    $p->addExtension($ext);
};
