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

    $rootDir = $p->getRootDir();
    $ext = (new Extension('swoole_git'))
        ->withAliasName('swoole')
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        ->withFile('swoole-git-submodule.tar.gz')
        ->withBuildLibraryCached(false) //及时更新 ext/swoole 的源代码
        # 打包 sapi/swoole 的源代码到 pool/ext/swoole-git-submodule.tar.gz
        # swoole 版本由子模块控制
        ->withDownloadScript(
            'swoole',
            <<<EOF
            cd {$rootDir}/sapi
EOF
        )
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo');
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};
