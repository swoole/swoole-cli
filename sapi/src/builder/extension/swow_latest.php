<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('swow_latest'))
            ->withOptions('--enable-swow  --enable-swow-ssl --enable-swow-curl ')
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://docs.toast.run/swow/en/install.html')
            ->withFile('swow-latest.tar.gz')
            //->withPeclVersion('1.2.0')
            ->withDownloadScript(
                "swow",
                <<<EOF
                git clone -b develop https://github.com/swow/swow.git
                mv swow swow-t
                mv swow-t/ext  swow
                rm -rf swow-t
EOF
            )
            ->withAliasName('swow')
            ->withDependentLibraries('openssl')
    );
};
