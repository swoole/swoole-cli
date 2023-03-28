<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    # Sanitizers 是谷歌开源的内存检测工具，包括AddressSanitizer、MemorySanitizer、ThreadSanitizer、LeakSanitizer。
    # Sanitizers是LLVM的一部分。
    # gcc不支持MemorySanitizer。
    # --enable-swow-address-sanitizer --enable-swow-memory-sanitizer --enable-swow-undefined-sanitizer

    # swow 启用不了
    $p->addExtension(
        (new Extension('swow'))
            ->withOptions('--enable-swow  --enable-swow-ssl --enable-swow-curl ')
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
            //->withFile('swow-v1.2.0.tar.gz')
            ->withPeclVersion('1.2.0')
            ->withDownloadScript(
                "swow",
                <<<EOF
                git clone -b v1.2.0 https://github.com/swow/swow.git
                mv swow swow-t 
                mv swow-t/ext  swow 
                rm -rf swow-t
EOF
            )
    );
};
