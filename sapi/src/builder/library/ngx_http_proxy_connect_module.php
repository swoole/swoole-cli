<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $lib = new Library('ngx_http_proxy_connect_module');
    $lib->withHomePage('https://github.com/chobits/ngx_http_proxy_connect_module.git')
        ->withLicense(
            'https://github.com/chobits/ngx_http_proxy_connect_module/blob/master/LICENSE',
            Library::LICENSE_BSD
        )
        ->withManual('https://github.com/chobits/ngx_http_proxy_connect_module.git')
        ->withUrl('https://github.com/chobits/ngx_http_proxy_connect_module/archive/refs/tags/v0.0.5.tar.gz')
        ->withFile('ngx_http_proxy_connect_module-v0.0.5.tar.gz')
        ->withDownloadScript(
            'ngx_http_proxy_connect_module',
            <<<EOF
            git clone -b v0.0.5 --depth 1 --progress   https://github.com/chobits/ngx_http_proxy_connect_module.git
EOF
        )
        ->withBuildScript('return 0')

    ;

    $p->addLibrary($lib);
};
