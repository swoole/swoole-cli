<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libxdp_prefix = EXAMPLE_PREFIX;
    $libxdp_prefix = LIBXDP_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    $lib = new Library('libxdp');
    $lib->withHomePage('https://github.com/xdp-project/xdp-tools.git')
        ->withLicense('https://github.com/xdp-project/xdp-tools/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withManual('https://github.com/xdp-project/xdp-tools.git')

        ->withFile('xdp-tools-latest.tar.gz')
        ->withDownloadScript(
            'xdp-tools',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/xdp-project/xdp-tools.git
EOF
        )
        ->withPrefix($libxdp_prefix)
        ->withPreInstallCommand('alpine',<<<EOF
        apk add llvm
        apk add bpftool
        apk add --no-cache grep
EOF
        )
        ->withBuildLibraryHttpProxy()
        ->withConfigure(
            <<<EOF

            ./configure --help

            ./configure \
            --prefix={$libxdp_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )

        ->withMakeOptions('libxdp')
        ->withPkgName('example')
        ->withBinPath($libxdp_prefix . '/bin/')
        ->withDependentLibraries('libpcap')

    ;

    $p->addLibrary($lib);

};
