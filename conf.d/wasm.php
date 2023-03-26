<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('wasm'))
            ->withOptions('--enable-wasm')
            ->withHomePage('https://github.com/wasmerio/php-ext-wasm')
            ->withLicense('https://github.com/wasmerio/wasmer-php/blob/master/LICENSE', Extension::LICENSE_MIT)
            ->withPeclVersion('0.5.0')
            ->withFile('wasm')
            ->withDownloadScript(
                <<<EOF
            git clone -b master --depth=1 https://github.com/wasmerio/wasmer-php 
            mv wasmer-php/ext wasm
EOF
            )
    );
    $workDir = $p->getWorkDir();
    $p->setVarable('cppflags', '$cppflags -I' . $workDir . '/ext/wasm/include');
};
