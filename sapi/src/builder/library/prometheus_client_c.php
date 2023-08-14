<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $prometheus_client_c_prefix = PROMETHEUS_CLIENT_C_PREFIX;
    $p->addLibrary(
        (new Library('prometheus_client_c'))
            ->withHomePage('https://github.com/digitalocean/prometheus-client-c.git')
            ->withLicense('https://github.com/digitalocean/prometheus-client-c/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withManual('https://digitalocean.github.io/prometheus-client-c/')
            ->withUrl('https://github.com/digitalocean/prometheus-client-c/archive/refs/tags/v0.1.3.tar.gz')
            ->withFile('prometheus-client-c-v0.1.3.tar.gz')
            ->withPrefix($prometheus_client_c_prefix)
            ->withBinPath($prometheus_client_c_prefix . '/bin/')
            ->withDependentLibraries('openssl', 'readline')
    );
};
