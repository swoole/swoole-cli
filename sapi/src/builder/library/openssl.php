<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $static = $p->isMacos() ? '' : ' -static --static';

    $cc = '';
    if ($p->isLinux() && ($p->get_C_COMPILER() === 'musl-gcc')) {

        # 参考 https://github.com/openssl/openssl/issues/7207#issuecomment-880121450
        # -idirafter /usr/include/ -idirafter /usr/include/x86_64-linux-gnu/"

        /*

        ln -sf /usr/include/linux/ /usr/include/x86_64-linux-musl/linux
        ln -sf /usr/include/x86_64-linux-gnu/asm/ /usr/include/x86_64-linux-musl/asm
        ln -sf /usr/include/asm-generic/ /usr/include/x86_64-linux-musl/asm-generic

        */
        $custom_include = '/usr/include/x86_64-linux-musl/';
        $cc = 'CC="${CC} -idirafter /usr/include/ -idirafter ' . $custom_include . '"';

    }


    $p->addLibrary(
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withManual('https://www.openssl.org/docs/')
            ->withUrl('https://github.com/openssl/openssl/releases/download/openssl-3.6.0/openssl-3.6.0.tar.gz')
            ->withFileHash('md5', '77ab78417082f22a2ce809898bd44da0')
            ->withPrefix($openssl_prefix)
            ->withConfigure(
                <<<EOF
                # Fix openssl error, "-ldl" should not be added when compiling statically
                sed -i.backup 's/add("-ldl", threads("-pthread"))/add(threads("-pthread"))/g' ./Configurations/10-main.conf
                # ./Configure LIST
               ./config {$static} no-tests no-shared  enable-tls1_3  --release \
               --prefix={$openssl_prefix} \
               --libdir=lib

EOF
            )
            ->withMakeOptions('build_sw')
            ->withMakeInstallCommand('install_sw')
            ->withScriptAfterInstall(
                <<<EOF
            sed -i.backup "s/-ldl/  /g" {$openssl_prefix}/lib/pkgconfig/libcrypto.pc

            sed -i.backup 's@\\\${libdir}/lib/@\\\${prefix}/lib/@g' {$openssl_prefix}/lib/pkgconfig/libcrypto.pc

            sed -i.backup '/^libdir/s/.*/libdir=\\\${prefix}\/lib/' {$openssl_prefix}/lib/pkgconfig/libcrypto.pc
            sed -i.backup '/^libdir/s/.*/libdir=\\\${prefix}\/lib/' {$openssl_prefix}/lib/pkgconfig/libssl.pc
            sed -i.backup '/^libdir/s/.*/libdir=\\\${prefix}\/lib/' {$openssl_prefix}/lib/pkgconfig/openssl.pc

            {$openssl_prefix}/bin/openssl version -a
EOF
            )
            ->withPkgName('libssl')
            ->withPkgName('libcrypto')
            ->withPkgName('openssl')
            ->withBinPath($openssl_prefix . '/bin/')
    );
};
