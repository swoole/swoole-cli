<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli'];

    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;

    $ext = (new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        ->withUrl('https://github.com/swoole/swoole-src/archive/refs/tags/v4.8.13.tar.gz')
        ->withFile('swoole-v4.8.13.tar.gz')
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
         git clone -b v4.8.13 --dept=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd');
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};
