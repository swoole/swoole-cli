<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares --enable-swoole-pgsql --with-brotli-dir=/usr/brotli ';

    // curl/imagemagick 对 brotli 静态库的支持有点问题，暂时关闭
    # $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares';
    //if ($p->getInputOption('with-brotli')) {
        //$options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    //}

    //exclude bypass skip ignore

    $p->addExtension((new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://github.com/swoole/swoole-src/releases')
        ->depends('curl', 'openssl', 'cares', 'zlib')
    );
};
