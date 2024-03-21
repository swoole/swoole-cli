<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $options = ' --enable-tonyenc ';

    $ext = (new Extension('tonyenc'))
        ->withOptions($options)
        ->withLicense('https://gitee.com/lfveeker/tonyenc/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://gitee.com/lfveeker/tonyenc.git')
        ->withManual('https://gitee.com/lfveeker/tonyenc')
        ->withHttpProxy(false)
        ->withBuildCached(false)
        ->withFile('tonyenc-v1.0.1.tar.gz')
        ->withDownloadScript(
            'tonyenc',
            <<<EOF
            git clone -b 1.0.1 --depth=1  https://gitee.com/lfveeker/tonyenc.git
EOF
        );
    $p->addExtension($ext);
};
