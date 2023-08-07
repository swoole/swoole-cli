<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2'];

    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;

    if ($p->getInputOption('with-swoole-pgsql')) {
        $options .= ' --enable-swoole-pgsql';
        $depends[] = 'pgsql';
    }

    $ext = (new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        ->withUrl('https://github.com/swoole/swoole-src/archive/refs/tags/v5.0.3.tar.gz')
        ->withFile('swoole-v5.0.3.tar.gz')
        ->withDownloadWithOriginURL() //明确申明 使用源地址下载
        ->withAutoUpdateFile() //明确声明，每次都执行下载，不使用已下载的缓存文件
        ->withHttpProxy(false) //明确申明 不使用代理
        ->withBuildLibraryCached(false) //明确申明 不使用缓存缓存目录  例子： ext/swoole (每次都全新解压源代码）
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo');
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};
