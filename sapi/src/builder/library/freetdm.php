<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freetdm_prefix = FREETDM_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $libpcap_prefix = LIBPCAP_PREFIX;
    $libpri_prefix = LIBPRI_PREFIX;
    $lib = new Library('freetdm');
    $lib->withHomePage('https://github.com/freeswitch/freetdm.git')
        ->withLicense('https://github.com/freeswitch/freetdm/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withManual('https://github.com/freeswitch/freetdm.git')
        ->withFile('freetdm-latest.tar.gz')
        ->withDownloadScript(
            'freetdm',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/freeswitch/freetdm.git
EOF
        )
        ->withBuildCached(false)
        ->withPrefix($freetdm_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($freetdm_prefix)
        ->withBuildScript(
            <<<EOF

           sh ./bootstrap

            ./configure --help


            ./configure \
            --prefix={$freetdm_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-64 \
            --with-libisdn \
            --with-libpri={$libpri_prefix} \
            --with-pcap={$libpcap_prefix} \
            --with-pcap-lib={$libpcap_prefix}/lib \
            --with-pcap-include={$libpcap_prefix}/include \
            --with-misdn \
            # -with-modinstdir=DIR

            make -j {$p->maxJob}
            # make install
            # make mod_freetdm
            # make mod_freetdm-install
EOF
        )
        ->withPkgName('ssl')
        ->withBinPath($freetdm_prefix . '/bin/')
        ->withDependentLibraries('libpcap', 'libpri', 'libisdn');

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $openssl_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $openssl_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lssl ');
};
