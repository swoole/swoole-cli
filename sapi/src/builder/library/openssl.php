<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $file = '';
    $url = '';
    $make_options = '';

    if ($php_version_id >= 8010) {
        $url = 'https://github.com/quictls/openssl/archive/refs/tags/openssl-3.0.10-quic1.tar.gz';
        $make_options = 'build_sw';
    } else {
        $url = 'https://github.com/openssl/openssl/releases/download/OpenSSL_1_1_1w/openssl-1.1.1w.tar.gz';
    }


    $openssl_prefix = OPENSSL_PREFIX;
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';


    $lib =
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withManual('https://www.openssl.org/docs/')
            ->withUrl($url)
            ->withPrefix($openssl_prefix)
            ->withConfigure(
                <<<EOF
                 # ./Configure LIST
               ./config {$static} no-shared  enable-tls1_3 --release \
               --prefix={$openssl_prefix} \
               --libdir={$openssl_prefix}/lib \
               --openssldir=/etc/ssl

EOF
            )
            ->withMakeInstallCommand('install_sw')
            ->withScriptAfterInstall(
                <<<EOF
            sed -i.backup "s/-ldl/  /g" {$openssl_prefix}/lib/pkgconfig/libcrypto.pc
EOF
            )
            ->withPkgName('libcrypto')
            ->withPkgName('libssl')
            ->withPkgName('openssl')
            ->withBinPath($openssl_prefix . '/bin/');

    if (!empty($make_options)) {
        call_user_func_array([$lib, 'withMakeOptions'], [$make_options]);
    }
    $p->addLibrary($lib);
};
