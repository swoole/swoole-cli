<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $riscv_prefix = RISCV_PREFIX;
    $lib = new Library('riscv_gnu_toolchain');
    $lib->withHomePage('https://github.com/riscv-collab/riscv-gnu-toolchain')
        ->withLicense('https://github.com/riscv-collab/riscv-gnu-toolchain/blob/master/LICENSE', Library::LICENSE_GPL)
        ->withManual('https://github.com/riscv-collab/riscv-gnu-toolchain')

        ->withFile('riscv-gnu-toolchain-latest.tar.gz')
        ->withDownloadScript(
            'riscv-gnu-toolchain',
            <<<EOF
                git clone -b master  --depth=1 --recursive https://github.com/riscv-collab/riscv-gnu-toolchain.git
EOF
        )
        ->withPrefix($riscv_prefix)

        ->withConfigure(
            <<<EOF
           ./configure --prefix=$riscv_prefix
EOF
        )
        ->withBinPath($riscv_prefix . '/bin/')


    ;
    $p->addLibrary($lib);
};
