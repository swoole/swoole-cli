<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // anaconda 安装包
    // https://repo.anaconda.com/archive/

    $options = '--enable-phpy ';

    $python_config = $p->getInputOption('with-python-config');
    $python_dir = $p->getInputOption('with-python-dir');
    $python_version = $p->getInputOption('with-python-version');
    if (!empty($python_config) && !empty($python_dir) && !empty($python_version)) {
        $options .= ' --with-python-version=' . $python_version;
        $options .= ' --with-python-dir=' . $python_dir;
        $options .= ' --with-python-config=' . $python_config;
    } else {
        throw new \Exception('phpy config python-config error ');
    }


    # $options .= ' --with-python-version=3.12';
    # $options .= ' --with-python-dir=/opt/anaconda3';

    $tag = 'v1.0.4';

    if (BUILD_PHP_VERSION_ID < 803000) {
        throw new \RuntimeException(" PHPY extension Only supports PHP 8.3.0 or higher");
    }

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
        ->withDependentExtensions('swoole')
        ->withDependentLibraries('curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2'); //'python3'g$p->addExtension($ext);

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $p->getPhpSrcDir() . '/ext/phpy/include');

    $python3_prefix = PYTHON3_PREFIX;
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $python3_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $python3_prefix . '/lib/');
    $p->withVariable('LIBS', '$LIBS -lpython3.12');


};
