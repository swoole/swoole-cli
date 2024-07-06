<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $swoole_tag = 'v6.0';
    $file = "swoole-{$swoole_tag}.tar.gz";

    $url = "https://github.com/swoole/swoole-src/archive/refs/tags/{$swoole_tag}.tar.gz";

    $dependentLibraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'sqlite3', 'unix_odbc', 'pgsql'];
    $dependentExtensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];
    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-swoole-coro-time ';
    # $options .= ' --enable-thread-context ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;
    $options .= ' --enable-swoole-pgsql ';
    $options .= ' --enable-swoole-sqlite ';
    $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';
    $options .= ' --enable-swoole-thread ';
    $options .= ' --enable-zts ';

    //linux 环境下 启用 opcache 扩展时构建报错，需要禁用 opcache

    if ($p->isLinux() && 0) {
        // 构建报错
        $options .= ' --enable-iouring ';
        $dependentLibraries[] = 'liburing';
        $p->withExportVariable('URING_CFLAGS', '$(pkg-config  --cflags --static  liburing)');
        $p->withExportVariable('URING_LIBS', '$(pkg-config    --libs   --static  liburing)');
    }


    $ext = (new Extension('swoole_v6'))
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withBuildCached(false)
        ->withDependentLibraries(...$dependentLibraries)
        ->withDependentExtensions(...$dependentExtensions);

    $p->addExtension($ext);

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);

    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');


    // 使用扩展钩子 下载 swoole v6 源码
    $p->withBeforeConfigureScript('swoole_v6', function (Preprocessor $p) {
        $workdir = $p->getWorkDir();
        $cmd = <<<EOF
        cd {$workdir}
        if [ -d {$workdir}/ext/swoole ] ; then
            rm -rf {$workdir}/ext/swoole
        fi
        mkdir -p {$workdir}/ext/
        mkdir -p {$workdir}/var/cache/ext/
        cd {$workdir}/var/cache/ext/
        test -d swoole && rm -rf swoole
        if [ -f {$workdir}/pool/ext/swoole-v6.0.0.tar.gz ] ; then
            mkdir swoole
            tar --strip-components=1 -C swoole -xf {$workdir}/pool/ext/swoole-v6.0.0.tar.gz
        else
            git clone -b master  https://github.com/swoole/swoole-src.git swoole
            cd swoole
            tar   -zcf {$workdir}/pool/ext/swoole-v6.0.0.tar.gz ./
            cd {$workdir}/var/cache/ext/
        fi
        cd {$workdir}/var/cache/ext/
        mv swoole {$workdir}/ext/
EOF;

        return $cmd;
    });
};
