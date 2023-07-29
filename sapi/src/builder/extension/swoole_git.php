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

    $rootDir = $p->getRootDir();
    $ext = (new Extension('swoole_git'))
        ->withAliasName('swoole')
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        ->withFile('swoole-git-submodule.tar.gz')
        ->withAutoUpdateFile(true)
        # 自动 拷贝子模块 swoole 的源代码到 pool/ext/ 目录
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
