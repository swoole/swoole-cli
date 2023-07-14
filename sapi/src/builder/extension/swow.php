<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $options = ' --enable-swow ';
    $options .= ' --enable-swow-ssl ';
    $options .= ' --enable-swow-curl ';
    $options .= ' --enable-swow-pdo-pgsql ';


    $p->addExtension(
        (new Extension('swow'))
            ->withOptions($options)
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
            ->withManual('https://docs.toast.run/swow/en/install.html')
            ->withFile('swow-v1.3.1.tar.gz')
            ->withDownloadScript(
                "swow",
                <<<EOF
                git clone -b v1.3.1 https://github.com/swow/swow.git
                mv swow swow-t
                mv swow-t/ext  swow
                rm -rf swow-t
EOF
            )
            ->withDependentLibraries('openssl', 'pgsql')
    );
};
