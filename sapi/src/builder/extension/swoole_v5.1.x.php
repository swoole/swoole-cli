<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'pgsql', 'unix_odbc', 'sqlite3'];
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;
    $options .= ' --enable-swoole-pgsql ';
    $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';
    $options .= ' --enable-swoole-sqlite ';

    $ext = (new Extension('swoole_v5.1.x'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo' );

    $ext->withDependentLibraries(...$depends);
    $p->addExtension($ext);

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);

    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');

    // 使用扩展钩子 下载 swoole v5.1.x 源码
    $p->withBeforeConfigureScript('swoole_v5', function (Preprocessor $p) {
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

        if [ -f {$workdir}/pool/ext/swoole-v5.1.x.tar.gz ] ; then
            mkdir swoole
            tar --strip-components=1 -C swoole -xf {$workdir}/pool/ext/swoole-v5.1.x.tar.gz
        else
            git clone -b 5.1.x  https://github.com/swoole/swoole-src.git swoole
            cd swoole
            tar   -zcf {$workdir}/pool/ext/swoole-v5.1.x.tar.gz ./
            cd {$workdir}/var/cache/ext/
        fi

        cd {$workdir}/var/cache/ext/
        mv swoole {$workdir}/ext/

EOF;

        return $cmd;
    });
};
