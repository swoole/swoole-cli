<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssh_prefix = OPENSSH_PREFIX;
    $lib = new Library('openssh');
    $lib->withHomePage('https://www.openssh.com/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_BSD)
        ->withUrl('https://ftp.openbsd.org/pub/OpenBSD/OpenSSH/openssh-9.3.tar.gz')
        ->withManual('https://www.openssh.com/portable.html')
        ->withFile('openssh-V_9_3_P1.tar.gz')
        ->withDownloadScript(
            'openssh',
            <<<EOF
                git clone -b V_9_3_P1  --depth=1 git://anongit.mindrot.org/openssh.git
EOF
        )
        ->withPrefix($openssh_prefix)
        ->withConfigure(
            <<<EOF
            autoreconf -fi
            ./configure --help

            PACKAGES='zlib openssl '
            PACKAGES="\$PACKAGES  "

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$openssh_prefix} \
            --with-pie



EOF
        )
        ->withBuildCached(false)
        ->withBinPath($openssh_prefix . '/bin/')
        ->withDependentLibraries('openssl', 'libedit', 'zlib')
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
