<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2'];

    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;


    $buildType = $p->getInputOption('with-build-type');
    if ($buildType == 'debug') {
        $options .= ' --enable-debug ';
        $options .= ' --enable-trace-log ';
        $options .= ' --enable-swoole-dev ';
    }

    if ($p->getInputOption('with-libpg')) {
        $options .= ' --enable-swoole-pgsql';
        $depends[] = 'pgsql';
    }
    $ext=(new Extension('swoole'))
            ->withOptions($options)
            ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://github.com/swoole/swoole-src/releases')
            ->withHomePage('https://github.com/swoole/swoole-src')
            ->depends('curl', 'openssl', 'cares', 'zlib', 'brotli')
            ->withHomePage('https://github.com/swoole/swoole-src')
            ->withFile('swoole-v5.0.3.tar.gz')
            ->withDownloadScript(
                'swoole-src',
                <<<EOF
            git clone -b v5.0.3 --depth=1 https://github.com/swoole/swoole-src
EOF
            )
    ;
    call_user_func_array([$ext,'depends'], $depends);
    $p->addExtension($ext);
};
