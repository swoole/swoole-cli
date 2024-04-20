<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    return null;//待改进
    $grpc_prefix = GRPC_PREFIX;
    $options = ' --enable-grpc=' . $grpc_prefix;
    $ext = (new Extension('grpc'))
        ->withOptions($options)
        ->withLicense('https://github.com/grpc/grpc/blob/master/src/php/ext/grpc/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('grpc.io')
        ->withManual('https://github.com/grpc/grpc/tree/master/src/php/ext')
        ->withFile('grpc-latest.tar.gz')
        ->withDownloadScript(
            'grpc', # 待打包目录名称
            <<<EOF
            git clone -b master --depth=1 https://github.com/grpc/grpc.git
            mv grpc/src/php/ext/grpc  grpc

EOF
        )
        ->withDependentLibraries('grpc')
    ;
    $p->addExtension($ext);
    $p->withExportVariable('GRPC_LIB_SUBDIR', GRPC_PREFIX . '/lib/');

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);
    $p->withVariable('CXXFLAGS', '$CXXFLAGS -std=c++17 ');
};
