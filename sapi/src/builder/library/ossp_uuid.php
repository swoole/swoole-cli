<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ossp_uuid_prefix = OSSP_UUID_PREFIX;
    $ldflags = $p->getOsType() == 'macos' ? '' : ' -static  ';

    $lib = new Library('ossp_uuid');
    $lib->withHomePage('http://www.ossp.org/pkg/lib/uuid/')
        ->withLicense('http://www.ossp.org/doc/license.html', Library::LICENSE_GPL)
        ->withFile('uuid-1.6.1.tar.gz')
        ->withUrl('ftp://ftp.ossp.org/pkg/lib/uuid/uuid-1.6.1.tar.gz')
        ->withHttpProxy(false)
        ->withPrefix($ossp_uuid_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ossp_uuid_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            cd contrib/uuid-ossp
EOF
        )
        ->withMakeInstallOptions('DESTDIR=' . $ossp_uuid_prefix)
        ->withBinPath($ossp_uuid_prefix . '/bin/');

    $p->addLibrary($lib);
};
