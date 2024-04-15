<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $python3_prefix = PYTHON3_PREFIX;

    $options = '--enable-phpy ';
    $options .= ' --with-python-version=3.12';
    # $options .= ' --with-python-dir=/opt/anaconda3';
    $options .= ' --with-python-dir=' . $python3_prefix;
    $options .= ' --with-python-config=' . $python3_prefix . '/bin/python3-config';

    $tag = 'v1.0.4';

    $ext = (new Extension('phpy'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/phpy/blob/main/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/phpy/')
        ->withManual('https://github.com/swoole/phpy/')
        ->withBuildCached(false)
        ->withFile('phpy-latest.tar.gz')
        ->withDownloadScript(
            'phpy', # 待打包目录名称
            <<<EOF
            git clone -b main --depth=1 https://github.com/swoole/phpy.git
EOF
        )
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo')
        ->withDependentLibraries('curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2', 'python3');
    $p->addExtension($ext);

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $p->getPhpSrcDir(). '/ext/phpy/include');


};


