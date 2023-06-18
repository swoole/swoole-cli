<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $socat_prefix = SOCAT_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('prometheus_client_c'))
            ->withHomePage('https://github.com/digitalocean/prometheus-client-c.git')
            ->withLicense('https://github.com/digitalocean/prometheus-client-c/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withUrl('https://github.com/digitalocean/prometheus-client-c/archive/refs/tags/v0.1.3.tar.gz')
            ->withFile('prometheus-client-c-v0.1.3.tar.gz')
            ->withBinPath($socat_prefix . '/bin/')
            ->withDependentLibraries('openssl', 'readline')
    );
};
