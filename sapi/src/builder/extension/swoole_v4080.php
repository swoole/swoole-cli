<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $swoole_tag = 'v4.8.13';
    $file = "swoole-v{$swoole_tag}.tar.gz";
    $dependentLibraries = ['curl', 'openssl', 'cares', 'zlib', 'brotli'];
    $dependentExtensions = ['curl', 'openssl', 'sockets', 'mysqlnd', 'pdo'];

    $options = ' --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --enable-http2  --enable-brotli  ';
    $options .= ' --with-openssl-dir=' . OPENSSL_PREFIX;
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --enable-swoole-json ' ;

    if (in_array($p->getBuildType(), ['dev', 'debug'])) {
        $options .= ' --enable-debug ';
        $options .= ' --enable-debug-log ';
        $options .= ' --enable-trace-log ';
    }

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
        ->withBuildCached(false);
    call_user_func_array([$ext, 'withDependentLibraries'], $dependentLibraries);
    call_user_func_array([$ext, 'withDependentExtensions'], $dependentExtensions);
    $p->addExtension($ext);
    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);

    $p->withExportVariable('CARES_CFLAGS', '$(pkg-config  --cflags --static  libcares)');
    $p->withExportVariable('CARES_LIBS', '$(pkg-config    --libs   --static  libcares)');
};
