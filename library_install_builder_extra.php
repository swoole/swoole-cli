<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_ovs(Preprocessor $p)
{
    $libgomp_prefix = '/usr/libgomp';
    $lib = new Library('ovs');
    $lib->withHomePage('https://gcc.gnu.org/projects/gomp/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('')
        ->withSkipDownload()
        ->withManual('https://gcc.gnu.org/onlinedocs/libgomp/')
        ->withPrefix($libgomp_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libgomp_prefix)
        ->withConfigure(
            <<<EOF
./configure --help
EOF
        )
        ->withPkgName('libgomp');

    $p->addLibrary($lib);
}

function install_ovn(Preprocessor $p)
{
    $libgomp_prefix = '/usr/libgomp';
    $lib = new Library('ovn');
    $lib->withHomePage('https://gcc.gnu.org/projects/gomp/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('')
        ->withSkipDownload()
        ->withManual('https://gcc.gnu.org/onlinedocs/libgomp/')
        ->withPrefix($libgomp_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libgomp_prefix)
        ->withConfigure(
            <<<EOF
./configure --help
EOF
        )
        ->withPkgName('libgomp');

    $p->addLibrary($lib);
}

function install_socat($p)
{
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
    $p->addLibrary(
        (new Library('socat'))
            ->withHomePage('http://www.dest-unreach.org/socat/')
            ->withLicense('http://www.dest-unreach.org/socat/doc/README', Library::LICENSE_GPL)
            ->withUrl('http://www.dest-unreach.org/socat/download/socat-1.7.4.4.tar.gz')
            ->withConfigure(
                '
            pkg-config --cflags --static readline
            pkg-config  --libs --static readline
            ./configure --help ;
            CFLAGS=$(pkg-config --cflags --static  libcrypto  libssl    openssl readline)
            export CFLAGS="-static -O2 -Wall -fPIC $CFLAGS "
            export LDFLAGS=$(pkg-config --libs --static libcrypto  libssl    openssl readline)
            # LIBS="-static -Wall -O2 -fPIC  -lcrypt  -lssl   -lreadline"
            # CFLAGS="-static -Wall -O2 -fPIC"
            ./configure \
            --prefix=/usr/socat \
            --enable-readline \
            --enable-openssl-base=/usr/openssl
            '
            )
            ->withSkipBuildInstall()
    );
}

function install_aria2($p)
{
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
    $p->addLibrary(
        (new Library('aria2'))
            ->withHomePage('https://aria2.github.io/')
            ->withLicense('https://github.com/aria2/aria2/blob/master/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/aria2/aria2/releases/download/release-1.36.0/aria2-1.36.0.tar.gz')
            ->withManual('https://aria2.github.io/manual/en/html/README.html')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
            # CFLAGS=$(pkg-config --cflags --static  libcrypto  libssl    openssl readline)
            # export CFLAGS="-static -O2 -Wall -fPIC $CFLAGS "
            # export LDFLAGS=$(pkg-config --libs --static libcrypto  libssl    openssl readline)
            # LIBS="-static -Wall -O2 -fPIC  -lcrypt  -lssl   -lreadline"
            # CFLAGS="-static -Wall -O2 -fPIC"
            export ZLIB_CFLAGS=$(pkg-config --cflags --static zlib) ;
            export  ZLIB_LIBS=$(pkg-config --libs --static zlib) ;
            ./configure --help ;
             ARIA2_STATIC=yes
            ./configure \
            --with-ca-bundle="/etc/ssl/certs/ca-certificates.crt" \
            --prefix=/usr/aria2 \
            --enable-static=yes \
            --enable-shared=no \
            --enable-libaria2 \
            --with-libuv \
            --without-gnutls \
            --with-openssl \
            --with-libiconv-prefix=/usr/libiconv/ \
            --with-libz
            # --with-tcmalloc
            '
            )
            ->withSkipBuildInstall()
    );
}
