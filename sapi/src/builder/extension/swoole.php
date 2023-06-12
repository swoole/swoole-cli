<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli'];

    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;

    if ($p->getInputOption('with-swoole-pgsql')) {
        $options .= ' --enable-swoole-pgsql';
        $depends[] = 'pgsql';
    }

    $ext = (new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        ->withDependExtension('curl', 'openssl', 'sockets', 'mysqlnd')
    ;
    call_user_func_array([$ext, 'depends'], $depends);
    $p->addExtension($ext);
};
