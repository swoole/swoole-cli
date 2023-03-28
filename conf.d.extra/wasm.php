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
            //->withFile('wasm-0.1.1.tgz')
            ->withPeclVersion('1.2.1')
            ->withDownloadScript(
                'wasmer-php',
                <<<EOF
            git clone -b master --depth=1 https://github.com/wasmerio/wasmer-php 
            mv wasmer-php/ext wasm
            rm -rf wasmer-php 
            mv wasm wasmer-php 
EOF
            )
    );
    $workDir = $p->getWorkDir();
    $p->withVariable('cppflags', '$cppflags -I' . $workDir . '/ext/wasm/include');
};
