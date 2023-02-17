<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // curl/imagemagick 对 brotli 静态库的支持有点问题，暂时关闭
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares';
    if ($p->getInputOption('with-brotli')) {
        $p->addLibrary(
            (new Library('brotli'))
                ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
                ->withFile('brotli-1.0.9.tar.gz')
                ->withPrefix(BROTLI_PREFIX)
                ->withConfigure('cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=OFF -DCMAKE_INSTALL_PREFIX=' . BROTLI_PREFIX . ' .')
                ->withScriptAfterInstall(
                    implode(PHP_EOL, [
                        'rm -rf ' . BROTLI_PREFIX . '/lib/*.so.*',
                        'rm -rf ' . BROTLI_PREFIX . '/lib/*.so',
                        'rm -rf ' . BROTLI_PREFIX . '/lib/*.dylib',
                        'mv ' . BROTLI_PREFIX . '/lib/libbrotlicommon-static.a ' . BROTLI_PREFIX . '/lib/libbrotli.a',
                        'mv ' . BROTLI_PREFIX . '/lib/libbrotlienc-static.a ' . BROTLI_PREFIX . '/lib/libbrotlienc.a',
                        'mv ' . BROTLI_PREFIX . '/lib/libbrotlidec-static.a ' . BROTLI_PREFIX . '/lib/libbrotlidec.a',
                    ]))
                ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
                ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
                ->withHomePage('https://github.com/google/brotli')
        );
        $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    }

    $p->addExtension((new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->depends('curl', 'openssl', 'cares', 'zlib')
    );
};
