<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $file = '';
    $url = '';
    $download_dir_name = '';
    $download_script = '';
    $dependent_libraries = ['curl', 'openssl', 'cares', 'zlib'];
    $dependent_extensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';

    if ($php_version_id >= 8010) {
        $file = 'swoole-v5.0.3.tar.gz';
        $url = 'https://github.com/swoole/swoole-src/archive/refs/tags/v5.0.3.tar.gz';

        $dependent_libraries = array_merge($dependent_libraries, ['brotli', 'nghttp2']);
        $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
        $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;

        if ($p->getInputOption('with-swoole-pgsql')) {
            $options .= ' --enable-swoole-pgsql';
            $dependent_libraries[] = 'pgsql';
        }

        $file = 'swoole-v5.1.0.tar.gz';
        $url = 'https://github.com/swoole/swoole-src/archive/refs/tags/v5.1.0.tar.gz';

        $options .= ' --enable-swoole-sqlite';
        $options .= ' --with-swoole-odbc=unixODBC,' . UNIX_ODBC_PREFIX . ' ';
        $options .= ' --enable-swoole-coro-time --enable-thread-context ';

        $dependent_libraries = array_merge($dependent_libraries, ['sqlite3', 'unix_odbc', 'pgsql']);
    } else {
        $file = 'swoole-4.8.x.tar.gz';
        $download_dir_name = 'swoole-src';
        $download_script = <<<EOF
        git clone -b 4.8.x --depth=1  https://github.com/swoole/swoole-src.git
EOF;
        if ($php_version_id < 8000) {
            $options .= ' --enable-http2 ';
            $options .= ' --enable-swoole-json ';
        }
        $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    }

    $ext = (new Extension('swoole'))
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withManual('https://wiki.swoole.com/#/')
        ->withOptions($options)
        ->withUrl($url)
        ->withFile($file)
        ->withBuildLibraryCached(false);
    call_user_func_array([$ext, 'withDependentLibraries'], $dependent_libraries);
    call_user_func_array([$ext, 'withDependentExtensions'], $dependent_extensions);
    if (!empty($download_dir_name)) {
        call_user_func_array([$ext, 'withDownloadScript'], [$download_dir_name, $download_script]);
    }

    $p->addExtension($ext);
};
