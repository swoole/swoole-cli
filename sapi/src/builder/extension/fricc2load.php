<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $options = ' --enable-fricc2load=static ';

    $ext = (new Extension('fricc2load'))->withOptions($options)
        ->withLicense('https://github.com/hoowa/PHP-FRICC2/blob/main/LICENSE', Extension::LICENSE_GPL)
        ->withHomePage('https://github.com/hoowa/PHP-FRICC2.git')
        ->withManual('https://github.com/hoowa/PHP-FRICC2.git')
        ->withFile('fricc2load-latest.tar.gz')
        ->withDownloadScript(
            'fricc2load',
            <<<EOF
            git clone -b main --depth=1 https://github.com/hoowa/PHP-FRICC2.git
            mv  PHP-FRICC2/fricc2load fricc2load

EOF
        )
        ->withDependentExtensions('zlib');

    $p->addExtension($ext);
};
