<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $protobuf_c_prefix = PROTOBUF_C_PREFIX;
    $protobuf_prefix = PROTOBUF_PREFIX;
    $lib = new Library('protobuf_c');
    $lib->withHomePage('https://github.com/protobuf-c/protobuf-c.git')
        ->withLicense('https://github.com/protobuf-c/protobuf-c/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/protobuf-c/protobuf-c.git')
        ->withFile('protobuf-c-latest.tar.gz')
        ->withDownloadScript(
            'protobuf-c',
            <<<EOF
            git clone -b master  --depth=1 https://github.com/protobuf-c/protobuf-c.git
EOF
        )
        ->withPrefix($protobuf_c_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh

        ./configure --help


        PACKAGES='protobuf '
        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$protobuf_prefix}/include/ " \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) " \
        ./configure \
        --prefix={$protobuf_c_prefix} \
        --enable-shared=no \
        --enable-static=yes

EOF
        )
        ->withPkgName('example')
        ->withBinPath($protobuf_c_prefix . '/bin/')
        ->withDependentLibraries('protobuf')
    ;

    $p->addLibrary($lib);
};
