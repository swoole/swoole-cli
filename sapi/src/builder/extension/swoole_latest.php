<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'pgsql', 'unixODBC'];

    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-swoole-pgsql --enable-swoole-coro-time ';
    $options .= ' --enable-swoole-sqlite ';
    $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;
    $options .= ' --enable-swoole-pgsql ';
    $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';
    if ($p->getInputOption('with-swoole-pgsql')) {
        $options .= ' ';
    }

    $ext = (new Extension('swoole_latest'))
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withFile('swoole-latest.tar.gz')
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
            git clone -b master --depth=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withManual('https://wiki.swoole.com/#/')
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo' )//'pdo_odbc'
        ->withAliasName('swoole');
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};
