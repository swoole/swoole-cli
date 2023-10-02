<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $swoole_tag = 'v4.8.13';
    $file = "swoole-v{$swoole_tag}.tar.gz";
    $dependent_libraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli'];
    $dependent_extensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];

    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-http2  --enable-brotli  ';
    $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;

    $ext = (new Extension('swoole_v4080'))
        ->withAliasName('swoole')
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withFile($file)
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
            git clone -b {$swoole_tag} --depth=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withBuildLibraryCached(false);
    call_user_func_array([$ext, 'withDependentLibraries'], $dependent_libraries);
    call_user_func_array([$ext, 'withDependentExtensions'], $dependent_extensions);
    $p->addExtension($ext);
};
