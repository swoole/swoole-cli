<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ovs_prefix = OVS_PREFIX;
    $lib = new Library('ovs');
    $lib->withHomePage('https://gcc.gnu.org/projects/gomp/')
        ->withLicense('https://github.com/openvswitch/ovs/blob/master/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('https://github.com/openvswitch/ovs/archive/refs/tags/v3.1.1.tar.gz')
        ->withFile('ovs-v3.1.1.tar.gz')
        ->withManual('https://github.com/openvswitch/ovs/blob/v3.1.1/Documentation/intro/install/general.rst')
        ->withPrefix($ovs_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ovs_prefix)
        ->withConfigure(
            <<<EOF
        set -x
        ./boot.sh
        ./configure --help
        PACKAGES="openssl"
        CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
        LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
        LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
        ./configure \
        --prefix={$ovs_prefix} \
        --enable-ssl \
        --enable-shared=no \
        --enable-static=yes

EOF
        )
        ->withPkgName('libofproto')
        ->withPkgName('libopenvswitch')
        ->withPkgName('libovsdb')
        ->withPkgName('libsflow')
        ->withBinPath($ovs_prefix . '/bin/')
        ->depends('openssl')
    ;

    $p->addLibrary($lib);
};
