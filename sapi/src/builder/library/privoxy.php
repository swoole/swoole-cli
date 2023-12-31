<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $privoxy_prefix = PRIVOXY_PREFIX;

    $cflags = $p->getOsType() == 'macos' ? "" : '-static';
    $options = $p->getOsType() == 'macos' ? "" : '--enable-static-linking';

    $p->addLibrary(
        (new Library('privoxy'))
            ->withHomePage('https://www.privoxy.org')
            ->withManual('https://www.privoxy.org/user-manual/quickstart.html')
            ->withManual('https://www.privoxy.org/user-manual/installation.html')
            ->withLicense('https://www.privoxy.org/gitweb/?p=privoxy.git;a=blob_plain;f=LICENSE.GPLv3;h=f288702d2fa16d3cdf0035b15a9fcbc552cd88e7;hb=HEAD', Library::LICENSE_GPL)
            ->withUrl('https://sourceforge.net/projects/ijbswa/files/Sources/3.0.34%20(stable)/privoxy-3.0.34-stable-src.tar.gz')
            /*
            ->withDownloadScript(
                'privoxy',
                <<<EOF
                    # gitweb
                    git clone -b master --progress  https://www.privoxy.org/git/privoxy.git

        EOF
            )
            */
            ->withFile('privoxy-3.0.34.tar.gz')
            ->withPrefix($privoxy_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($privoxy_prefix)
            //->withBuildCached(false)
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
                apk add w3m   docbook2x
                adduser privoxy --shell /sbin/nologin --disabled-password  --no-create-home
                adduser -H -D privoxy privoxy
EOF
            )
            ->withConfigure(
                <<<EOF

                autoheader
                autoconf
                ./configure --help
                set -x
                PACKAGES="openssl zlib"
                PACKAGES="\$PACKAGES  libbrotlicommon  libbrotlidec  libbrotlienc "
                PACKAGES="\$PACKAGES libpcre  libpcre16  libpcre32  libpcrecpp  libpcreposix"
                CFLAGS=" {$cflags} " \
                CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES )" \
                LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES )" \
                LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES )" \
                PCRE_STATIC=YES \
                ./configure \
                --prefix={$privoxy_prefix} \
                 {$options} \
                --with-openssl \
                --without-mbedtls \
                --with-brotli \
                --with-docbook=yes
 EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            cp -rf doc/webserver {$privoxy_prefix}/docs
EOF
            )
            ->withDependentLibraries('openssl', 'pcre', 'zlib', 'brotli')
    );
};
