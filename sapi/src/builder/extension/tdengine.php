<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $options = '--with-tdengine --with-tdengine-dir=' . TDENGINE_PREFIX;

    $ext = (new Extension('tdengine'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/Yurunsoft/php-tdengine.git')
        ->withManual('https://wiki.swoole.com/#/')
        ->withUrl('https://github.com/Yurunsoft/php-tdengine/archive/refs/tags/v1.0.6.tar.gz')
        ->withFile('php-tdengine-v1.0.6.tar.gz')
        ->withDownloadScript(
            'php-tdengine',
            <<<EOF
         git clone -b v1.0.6 --dept=1 https://github.com/Yurunsoft/php-tdengine.git
EOF
        )
        ->withDependentExtensions('swoole');
    $p->addExtension($ext);
};
