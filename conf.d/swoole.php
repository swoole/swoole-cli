<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares --enable-swoole-pgsql --with-brotli-dir=' . BROTLI_PREFIX;
    $p->addExtension(
        (new Extension('swoole'))
            ->withOptions($options)
            ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://github.com/swoole/swoole-src/releases')
            ->withHomePage('https://github.com/swoole/swoole-src')
            ->depends('curl', 'openssl', 'cares', 'zlib', 'brotli')
    );
};
