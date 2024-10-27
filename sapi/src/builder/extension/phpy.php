<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // anaconda 安装包
    // https://repo.anaconda.com/archive/


    # $options .= ' --with-python-version=3.12';
    # $options .= ' --with-python-dir=/opt/anaconda3';

    $tag = 'v1.0.4';


    $python3_prefix = PYTHON3_PREFIX;
    $options = [];
    $options[] = '--enable-phpy';
    $options[] = ' --with-python-version=3.12.2';
    $options[] = ' --with-python-dir=' . $python3_prefix;
    $options[] = ' --with-python-config=' . $python3_prefix . '/bin/python3-config';


    $dependentLibraries = ['python3'];
    $dependentExtensions = [];


    $ext = (new Extension('phpy'))
        ->withOptions(implode(' ', $options))
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
        ->withDependentExtensions(...$dependentExtensions)
        ->withDependentLibraries(...$dependentLibraries);
    $p->addExtension($ext);

    $p->withBeforeConfigureScript('phpy', function (Preprocessor $p) {
        $php_src = $p->getPhpSrcDir();
        $cmd = <<<EOF

        cd {$php_src}/
        sed -i.backup "s/ -z now/  /g" ext/phpy/config.m4
        rm -f ext/phpy/config.m4.backup
EOF;

        return $cmd;
    });


    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $p->getPhpSrcDir() . '/ext/phpy/include');
};
