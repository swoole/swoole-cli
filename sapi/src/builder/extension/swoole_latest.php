<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli'];

    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-http2  --enable-brotli  ';
    $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;


    $ext = (new Extension('swoole_latest'))
        ->withAliasName('swoole')
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withAutoUpdateFile(true) //每次都下载，不使用缓存，同时及时更新 ext/swoole 源码
        ->withFile('swoole-4.8.x-latest.tar.gz')
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
            git clone -b 4.8.x --depth=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo');
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};
