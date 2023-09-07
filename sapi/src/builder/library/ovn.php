<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $workdir = $p->getBuildDir();
    $ovs_prefix = OVS_PREFIX;
    $ovn_prefix = OVN_PREFIX;
    $lib = new Library('ovn');
    $lib->withHomePage('https://github.com/ovn-org/ovn.git')
        ->withLicense('https://github.com/ovn-org/ovn/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/ovn-org/ovn/blob/main/Documentation/intro/install/general.rst')
        //->withUrl('https://github.com/ovn-org/ovn/archive/refs/tags/v23.06.0.tar.gz')
        //->withFile('ovn-v23.06.0.tar.gz')
        //->withAutoUpdateFile()
        ->withFile('ovn-latest.tar.gz')
        ->withDownloadScript(
            'ovn',
            <<<EOF
            git clone -b main --depth=1 --progress https://github.com/ovn-org/ovn.git
EOF
        )
        ->withPrefix($ovn_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ovn_prefix)
        ->withBuildLibraryCached(false)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add mandoc man-pages
        apk add ghostscript
        pip3 install pipenv
EOF
        )
        ->withBuildScript(
            <<<EOF
        set -x
        sh ./boot.sh
        PACKAGES="openssl libsctp"
        CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
        LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
        LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
        ./configure  \
        --prefix={$ovn_prefix} \
        --enable-ssl \
        --enable-shared=no \
        --enable-static=yes \
        --with-ovs-source={$workdir}/ovs/ \
        --with-ovs-build={$workdir}/ovs/
        make -j {$p->maxJob}
        make install

        make dist-docs -j {$p->maxJob}
        # make docs-check -j {$p->maxJob}

        export PIPENV_PYPI_MIRROR=https://pypi.tuna.tsinghua.edu.cn/simple
        cd Documentation/
        pipenv --python 3
        # pipenv shell

        pipenv install -r requirements.txt
        pipenv install jinja2==3.0.0
        pipenv run python3 conf.py
EOF
        )
        ->withPkgName('ovn')
        ->withDependentLibraries('ovs', 'openssl', 'libsctp')
        ->withBinPath($ovn_prefix . '/bin/');

    $p->addLibrary($lib);
};
