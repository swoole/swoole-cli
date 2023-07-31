<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ovs_prefix = OVS_PREFIX;
    $lib = new Library('ovs');
    $lib->withHomePage('https://github.com/openvswitch/ovs/')
        ->withLicense('https://github.com/openvswitch/ovs/blob/master/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/openvswitch/ovs/blob/v3.1.1/Documentation/intro/install/general.rst')
        //->withUrl('https://github.com/openvswitch/ovs/archive/refs/tags/v3.1.1.tar.gz')
        //->withFile('ovs-v3.1.1.tar.gz')
        //->withAutoUpdateFile()
        ->withFile('ovs-latest.tar.gz')
        ->withDownloadScript(
            'ovs',
            <<<EOF
            git clone -b master --depth=1 --progress https://github.com/openvswitch/ovs.git
EOF
        )
        ->withPrefix($ovs_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ovs_prefix)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add mandoc man-pages
        apk add ghostscript
EOF
        )
        ->withBuildScript(
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
        # make -j {$p->maxJob}
        # make install


        make dist-docs -j {$p->maxJob}
        # make docs-check -j {$p->maxJob}

        cd Documentation/
        pipenv --python 3
        # pipenv shell
        pipenv install -r requirements.txt
        pipenv install jinja2==3.0.0
        pipenv run python3 conf.py


EOF
        )
        //->withMakeOptions( " dist-docs ")
        ->withPkgName('libofproto')
        ->withPkgName('libopenvswitch')
        ->withPkgName('libovsdb')
        ->withPkgName('libsflow')
        ->withBinPath($ovs_prefix . '/bin/')
        ->withDependentLibraries('openssl') //'dpdk'
    ;

    $p->addLibrary($lib);
};
