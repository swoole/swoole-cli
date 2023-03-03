<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // curl/imagemagick 对 brotli 静态库的支持有点问题，暂时关闭
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares';
    if ($p->getInputOption('with-brotli')) {
        $brotli_prefix = BROTLI_PREFIX;
        $p->addLibrary(
            (new Library('brotli'))
                ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
                ->withFile('brotli-1.0.9.tar.gz')
                ->withPrefix(BROTLI_PREFIX)
                ->withBuildScript(
                    <<<EOF
            cmake . -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX={$brotli_prefix} \
            -DBROTLI_SHARED_LIBS=OFF \
            -DBROTLI_STATIC_LIBS=ON \
            -DBROTLI_DISABLE_TESTS=ON \
            -DBROTLI_BUNDLED_MODE=OFF \
            && \
            cmake --build . --config Release --target install
EOF
                )
                ->withScriptAfterInstall(<<<EOF
            rm -rf {$brotli_prefix}/lib/*.so.*
            rm -rf {$brotli_prefix}/lib/*.so
            rm -rf {$brotli_prefix}/lib/*.dylib
            cp  -f {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotli.a
            mv     {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotlicommon.a
            mv     {$brotli_prefix}/lib/libbrotlienc-static.a    {$brotli_prefix}/lib/libbrotlienc.a
            mv     {$brotli_prefix}/lib/libbrotlidec-static.a    {$brotli_prefix}/lib/libbrotlidec.a
EOF
                )
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
