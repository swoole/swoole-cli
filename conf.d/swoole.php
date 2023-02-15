<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // curl/imagemagick 对 brotli 静态库的支持有点问题，暂时关闭
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares';
    if (getenv('SWOOLE_CLI_WITH_BROTLI')) {
        $p->addLibrary(
            (new Library('brotli'))
                ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
                ->withFile('brotli-1.0.9.tar.gz')
                ->withPrefix('/usr/brotli')
                ->withConfigure("cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=OFF -DCMAKE_INSTALL_PREFIX=/usr/brotli .")
                ->withScriptAfterInstall(
                    implode(PHP_EOL, [
                        'rm -rf /usr/brotli/lib/*.so.*',
                        'rm -rf /usr/brotli/lib/*.so',
                        'rm -rf /usr/brotli/lib/*.dylib',
                        'mv /usr/brotli/lib/libbrotlicommon-static.a /usr/brotli/lib/libbrotli.a',
                        'mv /usr/brotli/lib/libbrotlienc-static.a /usr/brotli/lib/libbrotlienc.a',
                        'mv /usr/brotli/lib/libbrotlidec-static.a /usr/brotli/lib/libbrotlidec.a',
                    ]))
                ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
                ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
                ->withHomePage('https://github.com/google/brotli')
        );
        $options .=  ' --with-brotli-dir=/usr/brotli';
    }

    $p->addExtension((new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->depends('curl', 'openssl', 'cares', 'zlib')
    );
};
