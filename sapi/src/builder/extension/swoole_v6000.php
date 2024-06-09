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
    if ($p->isLinux()) {
        $options .= ' --enable-iouring ';
        $dependentLibraries[] = 'liburing';
    }

    $options .= ' --enable-zts ';


    $ext = (new Extension('swoole_v6000'))
        ->withAliasName('swoole')
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withFile($file)
        ->withDownloadScript(
            'swoole-src',
            <<<EOF
            # git clone -b {$swoole_tag} --depth=1 https://github.com/swoole/swoole-src.git
            git clone -b master --depth=1 https://github.com/swoole/swoole-src.git
EOF
        )
        ->withBuildCached(false)
        ->withAutoUpdateFile()
        ->withDependentLibraries(...$dependentLibraries)
        ->withDependentExtensions(...$dependentExtensions);

    //call_user_func_array([$ext, 'withDependentLibraries'], $dependentLibraries);
    //call_user_func_array([$ext, 'withDependentExtensions'], $dependentExtensions);

    $p->addExtension($ext);

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);

    // 扩展钩子 写法 (下载 swoole v6 源码）
    $p->withBeforeConfigureScript('swoole_v6', function (Preprocessor $p) {
        $workdir = $p->getWorkDir();
        $cmd = <<<EOF
        cd {$workdir}
        # 临时解决 编译出现多重定义
        sed -i.backup 's/TSRMLS_CACHE_DEFINE();/TSRMLS_CACHE_EXTERN();/' ext/swoole/ext-src/swoole_thread.cc

EOF;

        return $cmd;
    });

    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');
};
