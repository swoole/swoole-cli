<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->withPeclVersion('1.5.4')
            ->withDownloadScript(
                'xlswriter',
                <<<EOF
            test -d php-ext-xlswriter && rm -rf php-ext-xlswriter
            git clone -b v1.5.4 --depth=1 --recursive https://github.com/viest/php-ext-xlswriter.git
            mv php-ext-xlswriter xlswriter
EOF
            )
            ->withOptions(' --with-xlswriter --enable-reader')
    );
};
