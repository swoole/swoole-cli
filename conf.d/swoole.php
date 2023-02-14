<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // curl/imagemagick 对 brotli 静态库的支持有点问题，暂时关闭
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares';
    if (getenv('SWOOLE_CLI_WITH_BROTLI')) {
        $options .=  ' --with-brotli-dir=/usr/brotli';
    }

    $p->addExtension((new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://github.com/swoole/swoole-src/releases')
    );
};
