<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_ovs(Preprocessor $p)
{
    $ovs_prefix = '/usr/ovs';
    $lib = new Library('ovs');
    $lib->withHomePage('https://gcc.gnu.org/projects/gomp/')
        ->withLicense('https://github.com/openvswitch/ovs/blob/master/LICENSE', Library::LICENSE_APACHE2)
        //->withUrl('https://github.com/openvswitch/ovs/archive/refs/tags/v3.1.0.tar.gz')
        ->withUrl('https://github.com/openvswitch/ovs/archive/refs/tags/v3.0.3.tar.gz')
        //->withFile('ovs-v3.1.0.tar.gz')
        ->withFile('ovs-v3.0.3.tar.gz')
        ->withManual('https://github.com/openvswitch/ovs/blob/master/Documentation/intro/install/general.rst')
        ->withPrefix($ovs_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ovs_prefix)
        ->withScriptBeforeConfigure(
            <<<EOF
          # apk add python3 
EOF
        )
        ->withConfigure(
            <<<EOF
        ./boot.sh
        ./configure --help

        ./configure \
        --prefix={$ovs_prefix} \
        --enable-ssl \
        --enable-shared=no \
        --enable-static=yes

EOF
        )
        ->withPkgName('libofproto  libopenvswitch libovsdb  libsflow')
        ->withBinPath($ovs_prefix . '/bin/');

    $p->addLibrary($lib);
}

function install_ovn(Preprocessor $p)
{
    $workdir=$p->getBuildDir();
    $ovs_prefix = '/usr/ovs';
    $ovn_prefix = '/usr/ovn';
    $lib = new Library('ovn');
    $lib->withHomePage('https://github.com/ovn-org/ovn.git')
        ->withLicense('https://github.com/ovn-org/ovn/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('https://github.com/ovn-org/ovn/archive/refs/tags/v22.09.1.tar.gz')
        ->withFile('ovn-v22.09.1.tar.gz')
        ->withManual('https://github.com/ovn-org/ovn/blob/main/Documentation/intro/install/general.rst')
        ->withPrefix($ovn_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ovn_prefix)
        ->withScriptBeforeConfigure(
            <<<EOF
          
EOF
        )
        ->withConfigure(
            <<<EOF
        sh ./boot.sh
        ./configure  \
        --prefix={$ovn_prefix} \
        --enable-ssl \
        --enable-shared=no \
        --enable-static=yes \
        --with-ovs-source={$workdir}/ovs/ \
        --with-ovs-build={$workdir}/ovs/
EOF
        )
        ->withPkgName('ovn')
        ->depends('ovs', 'openssl')
        ->withBinPath($ovn_prefix . '/bin/');

    $p->addLibrary($lib);
}

function install_socat($p)
{
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
    $socat_prefix = '/usr/socat';
    $openssl_prefix = OPENSSL_PREFIX;
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
            '. PHP_EOL .
                <<<EOF
            ./configure \
            --prefix=/usr/socat \
            --enable-readline \
            --enable-openssl-base={ $openssl_prefix}
EOF
            )
        ->withBinPath($socat_prefix . '/bin/')
    );
}

function install_aria2($p)
{
    $aria2_prefix = '/usr/aria2';
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
            export ZLIB_LIBS=$(pkg-config   --libs  --static zlib) ;
            
            export LIBUV_CFLAGS=$(pkg-config --cflags --static libuv) ;
            export LIBUV_LIBS=$(pkg-config   --libs   --static libuv) ;

            ./configure --help ;
         
             ARIA2_STATIC=yes ./configure \
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
        ->withBinPath($aria2_prefix . '/bin/')
    );
}

function install_dpdk(Preprocessor $p): void
{
    $dpdk_prefix = '/usr/dpdk';
    $p->addLibrary(
        (new Library('dpdk'))
            ->withHomePage('http://core.dpdk.org/')
            ->withLicense('https://core.dpdk.org/contribute/', Library::LICENSE_BSD)
            ->withUrl('https://fast.dpdk.org/rel/dpdk-22.11.1.tar.xz')
            ->withManual('http://core.dpdk.org/doc/')
            ->withManual('https://core.dpdk.org/doc/quick-start/')
            ->withUntarArchiveCommand('xz')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                <<<EOF
            apk add python3 py3-pip 
            pip3 install meson pyelftools -i https://pypi.tuna.tsinghua.edu.cn/simple
            # pip3 install meson pyelftools -ihttps://pypi.python.org/simple
EOF
            )
            ->withConfigure(
                <<<EOF
               
            meson  build
            ninja -C build
            ninja -C build
            ninja -C build install
            ldconfig
            pkg-config --modversion libdpdk
EOF
            )
            ->withBinPath($dpdk_prefix . '/bin/')
    );
}
function install_xdp(Preprocessor $p): void
{
    $xdp_prefix = '/usr/xdp';
    $p->addLibrary(
        (new Library('xdp'))
            ->withHomePage('https://github.com/xdp-project/xdp-tools.git')
            ->withLicense('https://github.com/xdp-project/xdp-tools/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/xdp-project/xdp-tools/archive/refs/tags/v1.3.1.tar.gz')
            ->withFile('xdp-v1.3.1.tar.gz')
            ->withManual('https://github.com/xdp-project/xdp-tutorial')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                <<<EOF
            apk add llvm bpftool
           
EOF
            )
            ->withConfigure(
                <<<EOF
cd lib/libxdp 
make libxdp


exit 0 
./configure 
exit 0 
EOF
            )
            ->withBinPath($xdp_prefix . '/bin/')
            ->withSkipBuildInstall()
    );
}
