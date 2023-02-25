<?php


function install_openssl_v3(Preprocessor $p)
{
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';
    $p->addLibrary(
        (new Library('openssl_v3', '/usr/openssl'))
            ->withUrl('https://www.openssl.org/source/openssl-3.0.7.tar.gz')
            ->withFile('openssl-3.0.7.tar.gz')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            # ./config $static \
            ./Configure   $static  \
            no-shared --release --prefix=/usr/openssl_v3
EOF
            )
            ->withMakeOptions('build_sw')
            ->withMakeInstallOptions('install_sw')
            ->withPkgConfig('/usr/openssl_v3/lib64/pkgconfig')
            ->withPkgName('libcrypto libssl openssl')
            ->withLdflags('-L/usr/openssl_v3/lib64')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withHomePage('https://www.openssl.org/')

    );
}

function install_openssl_v3_quic(Preprocessor $p)
{
    $static = $p->getOsType() === 'macos' ? '' : ' -static --static';
    $p->addLibrary(
        (new Library('openssl_v3_quic', '/usr/openssl'))
            ->withUrl('https://www.openssl.org/source/openssl-3.0.7.tar.gz')
            //https://github.com/quictls/openssl
            ->withFile('openssl-3.0.7.tar.gz')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            # ./config $static \
            ./Configure   $static  \
            no-shared --release --prefix=/usr/openssl_v3_quic
EOF
            )
            ->withMakeOptions('build_sw')
            ->withMakeInstallOptions('install_sw')
            ->withPkgConfig('/usr/openssl_v3_quic/lib64/pkgconfig')
            ->withPkgName('libcrypto libssl openssl')
            ->withLdflags('-L/usr/openssl_v3_quic/lib64')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withHomePage('https://curl.se/docs/http3.html')

    );
}

function install_libedit(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libedit'))
            ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
            ->withPrefix(LIBEDIT_PREFIX)
            ->withConfigure('./configure --prefix=' . LIBEDIT_PREFIX . ' --enable-static --disable-shared')
            ->withLdflags('')
            ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
            ->withHomePage('https://thrysoee.dk/editline/')
    );
}


function install_ninja(Preprocessor $p)
{
    $p->addLibrary(
        $lib = (new Library('ninja'))
            ->withHomePage('https://ninja-build.org/')
            //->withUrl('https://github.com/ninja-build/ninja/releases/download/v1.11.1/ninja-linux.zip')
            ->withUrl('https://github.com/ninja-build/ninja/archive/refs/tags/v1.11.1.tar.gz')
            ->withFile('ninja-build-v1.11.1.tar.gz')
            ->withLicense('https://github.com/ninja-build/ninja/blob/master/COPYING', Library::LICENSE_APACHE2)
            ->withManual('https://ninja-build.org/manual.html')
            ->withManual('https://github.com/ninja-build/ninja/wiki')
            ->withLabel('build_env_bin')
            //->withCleanBuildDirectory()
            //->withUntarArchiveCommand('unzip')
            ->withConfigure(
                "
                /usr/bin/ar -h 
                cmake -Bbuild-cmake -D CMAKE_AR=/usr/bin/ar
                cmake --build build-cmake
                mkdir -p /usr/ninja/bin/
                cp build-cmake/ninja /usr/ninja/bin/
                return 0 ;
                ./configure.py --bootstrap
                mkdir -p /usr/ninja/bin/
                cp ninja /usr/ninja/bin/
                return 0 ;
            "
            )
            ->withBinPath('/usr/ninja/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );

    if ($p->getOsType() == 'macos') {
        $lib->withUrl('https://github.com/ninja-build/ninja/releases/download/v1.11.1/ninja-mac.zip');
    }
}

function install_nettle($p)
{
    $p->addLibrary(
        (new Library('nettle'))
            ->withHomePage('https://www.lysator.liu.se/~nisse/nettle/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/nettle/nettle-3.8.tar.gz')
            ->withFile('nettle-3.8.tar.gz')
            ->withPrefix('/usr/nettle/')
            ->withConfigure(
                '
             ./configure --help
            ./configure \
            --prefix=/usr/nettle \
            --enable-static \
            --disable-shared
            '
            )
            ->withPkgName('nettle')
    );
}

function install_libtasn1($p)
{
    $libtasn1_prefix = LIBTASN1_PREFIX;
    $p->addLibrary(
        (new Library('libtasn1'))
            ->withHomePage('https://www.gnu.org/software/libtasn1/')
            ->withLicense('https://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/libtasn1/manual/')
            ->withUrl('https://ftp.gnu.org/gnu/libtasn1/libtasn1-4.19.0.tar.gz')
            ->withPrefix($libtasn1_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libtasn1_prefix} \
            --enable-static=yes \
            --enable-shared=no
EOF
            )
            ->withPkgName('libtasn1')
    );
}

function install_libexpat($p)
{
    $p->addLibrary(
        (new Library('libexpat'))
            ->withHomePage('https://github.com/libexpat/libexpat')
            ->withLicense('https://github.com/libexpat/libexpat/blob/master/COPYING', Library::LICENSE_MIT)
            ->withManual('https://libexpat.github.io/doc/')
            ->withUrl('https://github.com/libexpat/libexpat/releases/download/R_2_5_0/expat-2.5.0.tar.gz')
            ->withPrefix('/usr/libexpat/')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
             ./configure --help
            
            ./configure \
            --prefix=/usr/libexpat/ \
            --enable-static=yes \
            --enable-shared=no
            '
            )
            ->withPkgName('expat')
    );
}

function install_unbound($p)
{
    $p->addLibrary(
        (new Library('unbound'))
            ->withHomePage('https://nlnetlabs.nl/unbound')
            ->withLicense('https://github.com/NLnetLabs/unbound/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withManual('https://unbound.docs.nlnetlabs.nl/en/latest/')
            ->withUrl('https://nlnetlabs.nl/downloads/unbound/unbound-1.17.1.tar.gz')
            ->withPrefix('/usr/unbound/')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
                test -d /usr/unbound/ && rm -rf /usr/unbound/
            '
            )
            ->withConfigure(
                '
             ./configure --help
            
            ./configure \
            --prefix= \
            --enable-static=yes \
            --enable-shared=no \
            --with-libsodium=/usr/libsodium \
            --with-libnghttp2=/usr/nghttp2 \
            --with-nettle=/usr/nettle \
            --with-ssl=/usr/openssl \
            --with-libexpat=/usr/libexpat/ \
            --with-dynlibmodule=no \
            --with-libunbound-only 
          
            '
            )
            ->withPkgName('unbound')
    );
}

function install_gnutls($p)
{
    $note = <<<EOF

        Required libraries:
            libnettle crypto back-end
            gmplib arithmetic library1
            
        Optional libraries:
        libtasn1 ASN.1 parsing - a copy is included in GnuTLS
        p11-kit for PKCS #11 support
        trousers for TPM support
        libidn2 for Internationalized Domain Names support
        libunbound for DNSSEC/DANE functionality
EOF;


    $p->addLibrary(
        (new Library('gnutls'))
            ->withHomePage('https://www.gnutls.org/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://www.gnupg.org/ftp/gcrypt/gnutls/v3.7/gnutls-3.7.8.tar.xz')
            ->withManual('https://gitlab.com/gnutls/gnutls.git')
            ->withManual('https://www.gnutls.org/download.html')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/gnutls')
            ->withConfigure(
                '
                 test -d /usr/gnutls && rm -rf /usr/gnutls
                 set -uex 
                export GMP_CFLAGS=$(pkg-config  --cflags --static gmp)
                export GMP_LIBS=$(pkg-config    --libs   --static gmp)
                export LIBTASN1_CFLAGS=$(pkg-config  --cflags --static libtasn1)
                export LIBTASN1_LIBS=$(pkg-config    --libs   --static libtasn1)
                
                export LIBIDN2_CFLAGS=$(pkg-config  --cflags --static libidn2)
                export LIBIDN2_LIBS=$(pkg-config    --libs   --static libidn2)
                
                
                export LIBBROTLIENC_CFLAGS=$(pkg-config  --cflags --static libbrotlienc)
                export LIBBROTLIENC_LIBS=$(pkg-config    --libs   --static libbrotlienc)
                
                export LIBBROTLIDEC_CFLAGS=$(pkg-config  --cflags --static libbrotlidec)
                export LIBBROTLIDEC_LIBS=$(pkg-config    --libs   --static libbrotlidec)

                export LIBZSTD_CFLAGS=$(pkg-config  --cflags --static libzstd)
                export LIBZSTD_LIBS=$(pkg-config    --libs   --static libzstd)
                
                export P11_KIT_CFLAGS=$(pkg-config  --cflags --static p11-kit-1)
                export P11_KIT_LIBS=$(pkg-config    --libs   --static p11-kit-1)
            
              
            
                export CPPFLAGS=$(pkg-config    --cflags   --static libbrotlicommon libbrotlienc libbrotlidec)
                export LIBS=$(pkg-config        --libs     --static libbrotlicommon libbrotlienc libbrotlidec)
                 //  exit 0 
                # ./bootstrap
                ./configure --help 
             
             
                ./configure \
                --prefix=/usr/gnutls \
                --enable-static=yes \
                --enable-shared=no \
                --with-zstd \
                --with-brotli \
                --with-libiconv-prefix=/usr/libiconv \
                --with-libz-prefix=/usr/zlib \
                --with-libintl-prefix \
                --with-included-unistring \
                --with-nettle-mini  \
                --with-included-libtasn1 \
                --without-tpm2 \
                --without-tpm \
                --disable-doc \
                --disable-tests 
               # --with-libev-prefix=/usr/libev \
              
            '
            )->withPkgName('gnutls')
    //依赖：nettle, hogweed, libtasn1, libidn2, p11-kit-1, zlib, libbrotlienc, libbrotlidec, libzstd -lgmp  -latomic
    );
}


function install_boringssl($p)
{
    $p->addLibrary(
        (new Library('boringssl'))
            ->withHomePage('https://boringssl.googlesource.com/boringssl/')
            ->withLicense(
                'https://boringssl.googlesource.com/boringssl/+/refs/heads/master/LICENSE',
                Library::LICENSE_BSD
            )
            ->withUrl('https://github.com/google/boringssl/archive/refs/heads/master.zip')
            ->withFile('latest-boringssl.zip')
            ->withSkipDownload()
            ->withMirrorUrl('https://boringssl.googlesource.com/boringssl')
            ->withMirrorUrl('https://github.com/google/boringssl.git')
            ->withManual('https://boringssl.googlesource.com/boringssl/+/refs/heads/master/BUILDING.md')
            ->withUntarArchiveCommand('unzip')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/boringssl')
            ->withScriptBeforeConfigure(
                '
                 test -d /usr/boringssl && rm -rf /usr/boringssl
                '
            )
            ->withConfigure(
                '
                cd boringssl-master
                mkdir build
                cd build
                cmake -GNinja .. -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=0 -DCMAKE_INSTALL_PREFIX=/usr/boringssl
               
                cd ..
                # ninja
                ninja -C build
                
                ninja -C build install
            '
            )
            ->withSkipMakeAndMakeInstall()
            ->disableDefaultPkgConfig()
    //->withSkipBuildInstall()
    );
}

function install_wolfssl($p)
{
    $p->addLibrary(
        (new Library('wolfssl'))
            ->withHomePage('https://github.com/wolfSSL/wolfssl.git')
            ->withLicense('https://github.com/wolfSSL/wolfssl/blob/master/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/wolfSSL/wolfssl/archive/refs/tags/v5.5.4-stable.tar.gz')
            ->withFile('wolfssl-v5.5.4-stable.tar.gz')
            ->withManual('https://wolfssl.com/wolfSSL/Docs.html')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/wolfssl')
            ->withScriptBeforeConfigure(
                '
                 test -d /usr/wolfssl && rm -rf /usr/wolfssl
                '
            )
            ->withConfigure(
                <<<EOF
                ./autogen.sh
                ./configure --help
                
                ./configure  --prefix=/usr/wolfssl \
                --enable-static=yes \
                --enable-shared=no \
                --enable-all

EOF
            )
            //->withSkipMakeAndMakeInstall()
            ->withPkgName('wolfssl')
    //->withSkipBuildInstall()
    );
}

function install_nghttp3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('nghttp3'))
            ->withHomePage('https://github.com/ngtcp2/nghttp3')
            ->withManual('https://nghttp2.org/nghttp3/')
            ->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/tags/v0.8.0.tar.gz')
            //->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/heads/main.zip')
            ->withFile('nghttp3-v0.8.0.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/nghttp3')
            ->withConfigure(
                '
                export GNUTLS_CFLAGS=$(pkg-config  --cflags --static gnutls)
                export GNUTLS_LIBS=$(pkg-config    --libs   --static gnutls)
           
            autoreconf -fi
            ./configure --help 
          
            ./configure --prefix=/usr/nghttp3 --enable-lib-only \
            --enable-shared=no \
            --enable-static=yes 
            
        '
            )
            ->withLicense('https://github.com/ngtcp2/nghttp3/blob/main/COPYING', Library::LICENSE_MIT)
            ->withPkgName('libnghttp3')
    );
}

function install_ngtcp2(Preprocessor $p)
{
    //libexpat pcre2 libidn2 brotli

    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/archive/refs/tags/v0.13.1.tar.gz')
            ->withFile('ngtcp2-v0.13.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/ngtcp2')
            ->withConfigure(
                '

            # openssl does not have QUIC interface, disabling it
            # 
            # OPENSSL_CFLAGS=$(pkg-config  --cflags --static openssl)
            # OPENSSL_LIBS=$(pkg-config    --libs   --static openssl)
            
           
            export GNUTLS_CFLAGS=$(pkg-config  --cflags --static gnutls)
            export GNUTLS_LIBS=$(pkg-config    --libs   --static gnutls)
            export LIBNGHTTP3_CFLAGS=$(pkg-config  --cflags --static libnghttp3)
            export LIBNGHTTP3_LIBS=$(pkg-config    --libs   --static libnghttp3)
           
            export LIBEV_CFLAGS="-I/usr/libev/include"
            export LIBEV_LIBS="-L/usr/libev/lib -lev"
            
             autoreconf -fi
            ./configure --help 
          
            ./configure \
            --prefix=/usr/ngtcp2 \
            --enable-shared=no \
            --enable-static=yes \
            --with-gnutls=yes \
            --with-libnghttp3=yes \
            --with-libev=yes 
            '
            )
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withPkgName('libngtcp2  libngtcp2_crypto_gnutls')
    );
}

function install_quiche(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('quiche'))
            ->withHomePage('https://github.com/cloudflare/quiche')
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/cloudflare/quiche/archive/refs/heads/master.zip')
            ->withFile('latest-quiche.zip')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->withPrefix('/usr/quiche')
            ->withScriptBeforeConfigure(
                '
             test  -d /usr/quiche && rm -rf /usr/quiche
             # export RUSTUP_DIST_SERVER=https://mirrors.tuna.edu.cn/rustup
             # export RUSTUP_UPDATE_ROOT=https://mirrors.tuna.edu.cn/rustup/rustup
             export http_proxy=http://192.168.3.26:8015
             export https_proxy=http://192.168.3.26:8015
             source /root/.cargo/env
             cp -rf /work/pool/lib/boringssl /work/thirdparty/quiche/
             export OPENSSL_DIR=/usr/openssl
             export OPENSSL_STATIC=Yes
          
            '
            )
            ->withConfigure(
                '
            cd quiche-master
            cargo build --help 
            
            export QUICHE_BSSL_PATH=/work/thirdparty/quiche/boringssl
            cargo build --package quiche --release --features ffi,pkg-config-meta,qlog
            mkdir -p quiche/deps/boringssl/src/lib
            ln -vnf $(find target/release -name libcrypto.a -o -name libssl.a) quiche/deps/boringssl/src/lib/
            exit 0 
        
            '
            )
            ->withLicense('https://github.com/cloudflare/quiche/blob/master/COPYING', Library::LICENSE_BSD)
            ->withPkgName('quiche')
    );
}

function install_msh3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('msh3'))
            ->withHomePage('https://github.com/nibanks/msh3')
            ->withManual('https://github.com/nibanks/msh3.git')
            ->withUrl('https://github.com/nibanks/msh3/archive/refs/heads/main.zip')
            ->withFile('latest-msh3.zip')
            ->withFile('msh3')
            ->withSkipDownload()
            //->withCleanBuildDirectory()
            ->withUntarArchiveCommand('mv')
            ->withPrefix('/usr/msh3')
            ->withScriptBeforeConfigure(
                '
              cp -rf /work/pool/lib/msh3 /work/thirdparty/msh3
              apk add bsd-compat-headers
            '
            )
            ->withConfigure(
                <<<EOF
            cd /work/thirdparty/msh3/msh3
            pwd
            ls -lh 
            mkdir build && cd build
            #  cmake -G 'Unix Makefiles' -DCMAKE_BUILD_TYPE=RelWithDebInfo .. -DCMAKE_INSTALL_PREFIX=/usr/
            cmake -G 'Unix Makefiles'  -DCMAKE_BUILD_TYPE=Release  .. -DBUILD_SHARED_LIBS=0 -DCMAKE_INSTALL_PREFIX=/usr/msh3 
            cmake --build .
            cmake --install .

EOF
            )
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withPkgName('msh3')
    );
}

function install_coreutils($p)
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

     */
    $p->addLibrary(
        (new Library('gnu_coreutils'))
            ->withHomePage('https://www.gnu.org/software/coreutils/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/coreutils/')
            ->withUrl('https://mirrors.aliyun.com/gnu/coreutils/coreutils-9.1.tar.gz')
            ->withFile('coreutils-9.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
                ./bootstrap
                ./configure --help
                exit 0 
                  export FORCE_UNSAFE_CONFIGURE=1 
                  ./configure --prefix=/usr/gnu_coreutils \
                  --with-openssl=yes \
                  --with-libiconv-prefix=/usr/libiconv \
                  --with-libintl-prefix
             
            
            '
            )
            //->withSkipMakeAndMakeInstall()
            ->withPkgConfig('')
            ->withPkgName('')
    );
}

function install_gnulib($p)
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

     */
    $p->addLibrary(
        (new Library('gnulib'))
            ->withHomePage('https://www.gnu.org/software/gnulib/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/gnulib/manual/')
            ->withUrl('https://github.com/coreutils/gnulib/archive/refs/heads/master.zip')
            ->withFile('latest-gnulib.zip')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->withConfigure(
                '
               cd gnulib-master
             ./gnulib-tool --help
             return 0 ;
            '
            )
            ->withSkipMakeAndMakeInstall()
            ->withPkgConfig('')
            ->withPkgName('')
    );
}

function install_libunistring($p)
{
    $p->addLibrary(
        (new Library('libunistring'))
            ->withHomePage('https://www.gnu.org/software/libunistring/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/libunistring/libunistring-0.9.1.1.tar.gz')
            ->withFile('libunistring-0.9.1.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
            
            apk add coreutils
            
            test -d /usr/libunistring && rm -rf /usr/libunistring
            '
            )
            ->withConfigure(
                '
             ./configure --help
            
            ./configure \
            --prefix=/usr/libunistring \
            --enable-static \
            --disable-shared \
             --with-libiconv-prefix=/usr/libiconv 
            '
            )
            ->withPkgConfig('/usr/libunistring/lib/pkgconfig')
            ->withPkgName('libunistringe')
    );
}

function install_libevent($p)
{
    $p->addLibrary(
        (new Library('libevent'))
            ->withHomePage('https://github.com/libevent/libevent')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl(
                'https://github.com/libevent/libevent/releases/download/release-2.1.12-stable/libevent-2.1.12-stable.tar.gz'
            )
            ->withManual('https://libevent.org/libevent-book/')
            ->withPrefix('/usr/libevent')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            # 查看更多选项
            # cmake -LAH .
        mkdir build && cd build
        cmake ..   \
        -DCMAKE_INSTALL_PREFIX=/usr/libevent \
        -DEVENT__DISABLE_DEBUG_MODE=ON \
        -DCMAKE_BUILD_TYPE=Release \
        -DEVENT__LIBRARY_TYPE=STATIC  
  
EOF

            )
            ->withPkgName('libevent')
    );
}

function install_libuv($p)
{
    //as epoll/kqueue/event ports/inotify/eventfd/signalfd support
    $p->addLibrary(
        (new Library('libev'))
            ->withHomePage('http://software.schmorp.de/pkg/libev.html')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('http://dist.schmorp.de/libev/libev-4.33.tar.gz')
            ->withManual('http://cvs.schmorp.de/libev/README')
            ->withPrefix('/usr/libev')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            ls -lh 
            ./configure --help 
            ./configure --prefix=/usr/libev \
            --enable-shared=no \
            --enable-static=yes
           
EOF

            )
            ->withPkgName('libev')
    //->withSkipBuildInstall()
    );
}

function install_libev($p)
{
    $p->addLibrary(
        (new Library('libev'))
            ->withHomePage('http://software.schmorp.de/pkg/libev.html')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('http://dist.schmorp.de/libev/libev-4.33.tar.gz')
            ->withManual('http://cvs.schmorp.de/libev/README')
            ->withPrefix('/usr/libev')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            ls -lh 
            ./configure --help 
            ./configure --prefix=/usr/libev \
            --enable-shared=no \
            --enable-static=yes
           
EOF

            )
            ->withPkgName('libev')

    );
}

function install_libunwind($p)
{
    $p->addLibrary(
        (new Library('libunwind'))
            ->withHomePage('https://github.com/libunwind/libunwind.git')
            ->withLicense('https://github.com/libunwind/libunwind/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withUrl('https://github.com/libunwind/libunwind/releases/download/v1.6.2/libunwind-1.6.2.tar.gz')
            ->withFile('libunwind-1.6.2.tar.gz')
            ->withPrefix('/usr/libunwind')
            ->withConfigure(
                '
                 autoreconf -i
                 
                ./configure --help ;
                ./configure \
                --prefix=/usr/libunwind \
                --enable-static=yes \
                --enable-shared=no
                '
            )
            ->withPkgName('libunwind-coredump  libunwind-generic   libunwind-ptrace    libunwind-setjmp    libunwind')
            ->withSkipBuildInstall()
    );
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

function install_jemalloc($p)
{
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
    $p->addLibrary(
        (new Library('jemalloc'))
            ->withHomePage('http://jemalloc.net/')
            ->withLicense(
                'https://github.com/jemalloc/jemalloc/blob/dev/COPYING',
                Library::LICENSE_GPL
            )
            ->withUrl('https://github.com/jemalloc/jemalloc/archive/refs/tags/5.3.0.tar.gz')
            ->withFile('jemalloc-5.3.0.tar.gz')
            ->withConfigure(
                '
            sh autogen.sh
            ./configure --help ;
            ./configure \
            --prefix=/usr/jemalloc \
            --enable-static=yes \
            --enable-shared=no \
            --with-static-libunwind=/usr/libunwind/lib/libunwind.a
            '
            )
            ->withPkgConfig('/usr/jemalloc/lib/pkgconfig')
            ->withPkgName('jemalloc')
            ->withLdflags('/usr/jemalloc/lib')
            ->withSkipBuildInstall()
    );
}

function install_tcmalloc($p)
{
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
    $p->addLibrary(
        (new Library('tcmalloc'))
            ->withHomePage('https://google.github.io/tcmalloc/overview.html')
            ->withLicense('https://github.com/google/tcmalloc/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withUrl('https://github.com/google/tcmalloc/archive/refs/heads/master.zip')
            ->withFile('tcmalloc.zip')
            ->withUntarArchiveCommand('unzip')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
            cd  tcmalloc-master/
            bazel help
            bazel build
            return
            ./configure \
            --prefix=/usr/tcmalloc \
            --enable-static \
            --disable-shared
            '
            )
            ->withPkgConfig('/usr/tcmalloc/lib/pkgconfig')
            ->withPkgName('tcmalloc')
            ->withLdflags('/usr/tcmalloc/lib')
            ->withSkipBuildInstall()
    );
}

function install_aria2($p)
{
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
    $p->addLibrary(
        (new Library('aria2c'))
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

function install_bazel(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('bazel'))
            ->withHomePage('https://bazel.build')
            ->withLicense('https://github.com/bazelbuild/bazel/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withUrl('https://github.com/bazelbuild/bazel/releases/download/6.0.0/bazel-6.0.0-linux-x86_64')
            ->withManual('/usr/bazel/bin/')
            ->withManual('https://bazel.build/install')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('mv')
            ->withScriptBeforeConfigure(
                '
                test -d /usr/bazel/bin/ || mkdir -p /usr/bazel/bin/
                mv bazel /usr/bazel/bin/
                chmod a+x /usr/bazel/bin/bazel
                return 0 
               '
            )
            ->disableDefaultPkgConfig()
            ->disablePkgName()
            ->disableDefaultLdflags()
            ->withSkipBuildInstall()
    );
}

function install_libelf(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libelf'))
            ->withHomePage('https://github.com/WolfgangSt/libelf.git')
            ->withLicense('https://github.com/WolfgangSt/libelf/blob/master/COPYING.LIB', Library::LICENSE_GPL)
            ->withUrl('https://github.com/libbpf/libbpf/archive/refs/tags/v1.1.0.tar.gz')
            ->withFile('libbpf-v1.1.0.tar.gz')
            ->withManual('https://github.com/WolfgangSt/libelf.git')
            ->withPrefix('/usr/libelf')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                pwd
                test -d {$p->getBuildDir()}/libelf && rm -rf {$p->getBuildDir()}/libelf
                cp -rf {$p->getWorkDir()}/pool/lib/libelf {$p->getBuildDir()}/
                cd {$p->getBuildDir()}/libelf
                ./configure --help 
                ./configure --prefix=/usr/libelf \
                --enable-compat \
                --enable-shared=no 
  
EOF
            )
            ->withMakeInstallCommand('install-local')
            ->withPkgName('libelf')
    );
}

function install_libbpf(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libbpf'))
            ->withHomePage('https://github.com/libbpf/libbpf.git')
            ->withLicense('https://github.com/libbpf/libbpf/blob/master/LICENSE.BSD-2-Clause', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/libbpf/libbpf/archive/refs/tags/v1.1.0.tar.gz')
            ->withFile('libbpf-v1.1.0.tar.gz')
            ->withManual('https://libbpf.readthedocs.io/en/latest/api.html')
            ->withPrefix('/usr/libbpf')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                cd src
                BUILD_STATIC_ONLY=y  make 
                exit 0 
                mkdir build /usr/libbpf
                BUILD_STATIC_ONLY=y OBJDIR=build DESTDIR=/usr/libbpf make install
                eixt 0 
    
EOF
            )
            ->withPkgName('libbpf')
    );
}

function install_valgrind(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('valgrind'))
            ->withHomePage('https://valgrind.org/')
            ->withLicense('https://github.com/libbpf/libbpf/blob/master/LICENSE.BSD-2-Clause', Library::LICENSE_LGPL)
            ->withUrl('https://sourceware.org/pub/valgrind/valgrind-3.20.0.tar.bz2')
            ->withManual('https://valgrind.org/docs/man')
            ->withPrefix('/usr/valgrind')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

./autogen.sh
./configure --prefix=/usr/valgrind

  
EOF

            )
            ->withPkgName('valgrind')
            ->withBinPath('/usr/valgrind/bin/')
    );
}

function install_snappy(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('valgrind'))
            ->withHomePage('https://github.com/google/snappy')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://github.com/google/snappy/archive/refs/tags/1.1.9.tar.gz')
            ->withFile('snappy-1.1.9.tar.gz')
            ->withManual('https://github.com/google/snappy/blob/main/README.md')
            ->withPrefix('/usr/snappy')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

git submodule update --init
mkdir build
cd build && cmake ../ && make

  
EOF

            )
            ->withPkgName('snappy')
            ->withBinPath('/usr/snappy/bin/')
    );
}

function install_kerberos(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('kerberos'))
            ->withHomePage('https://web.mit.edu/kerberos/')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://kerberos.org/dist/krb5/1.20/krb5-1.20.1.tar.gz')
            ->withFile('krb5-1.20.1.tar.gz')
            ->withManual('https://web.mit.edu/kerberos/krb5-1.20/README-1.20.1.txt')
            //源码包： doc/html/admin/install.html
            ->withPrefix('/usr/kerberos')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
pwd
exit 0 
  
EOF

            )
            ->withPkgName('kerberos')
            ->withBinPath('/usr/kerberos/bin/')
    );
}

function install_fontconfig(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('fontconfig'))
            ->withHomePage('https://www.freedesktop.org/wiki/Software/fontconfig/')
            ->withLicense('https://www.freedesktop.org/software/fontconfig/webfonts/Licen.TXT', Library::LICENSE_SPEC)
            //->withUrl('https://gitlab.freedesktop.org/fontconfig/fontconfig/-/archive/main/fontconfig-main.tar.gz')
            ->withUrl('https://gitlab.freedesktop.org/fontconfig/fontconfig/-/tags/2.14.2')
            //download font https://www.freedesktop.org/software/fontconfig/webfonts/webfonts.tar.gz
            ->withFile('fontconfig-2.14.2.tar.gz')
            ->withManual('https://gitlab.freedesktop.org/fontconfig/fontconfig')
            ->withPrefix('/usr/fontconfig')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
pwd
exit 0 
  
EOF

            )
            ->withPkgName('fontconfig')
            ->withBinPath('/usr/fontconfig/bin/')
    );
}


function install_p11_kit(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('p11_kit'))
            ->withHomePage('https://github.com/p11-glue/p11-kit.git')
            ->withLicense('https://github.com/p11-glue/p11-kit/blob/master/COPYING', Library::LICENSE_BSD)
            ->withManual('https://p11-glue.github.io/p11-glue/p11-kit.html')
            ->withManual('https://p11-glue.github.io/p11-glue/p11-kit/manual/devel-building.html')
            ->withUrl('https://github.com/p11-glue/p11-kit/archive/refs/tags/0.24.1.tar.gz')
            //构建选项参参考文档： https://mesonbuild.com/Builtin-options.html
            ->withFile('p11-kit-0.24.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/p11_kit/')
            ->withConfigure(
                '
          
                # apk add python3 py3-pip  gettext  coreutils
                # pip3 install meson  -i https://pypi.tuna.tsinghua.edu.cn/simple
            
            echo $PATH;
            #./autogen.sh
            #./configure --help
            # --with-libtasn1 --with-libffi
           
            # meson setup -Dprefix=/usr/p11_kit/ -Dsystemd=disabled -Dbash_completion=disabled  --reconfigure  _build
            # run "ninja reconfigure" or "meson setup --reconfigure"
            # ninja reconfigure -C _build
            # meson setup --reconfigure _build
            meson setup  -Dprefix=/usr/p11_kit/ -Dsystemd=disabled    -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Dprefer_static=true \
            -Ddebug=false \
            -Dunity=off \
             _build
             
           
            # meson setup --wipe
            
            meson compile -C _build
            
           # DESTDIR=/usr/p11_kit/  meson install -C _build
            meson install -C _build
            exit 0 
            '
            )
            ->withBypassMakeAndMakeInstall()
            ->withPkgName('p11_kit')
    );
}

function install_pcre2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('pcre2', '/usr/pcre2'))
            ->withUrl('https://github.com/PCRE2Project/pcre2/releases/download/pcre2-10.42/pcre2-10.42.tar.gz')
            ->withFile('pcre2-10.42.tar.gz')
            ->withSkipInstall()
            //  CFLAGS='-static -O2 -Wall'
            ->withConfigure(
                "
            ./configure --help
            ./configure \
            --prefix=/usr/pcre2 \
            --enable-static \
            --disable-shared \
            --enable-pcre2-16 \
            --enable-pcre2-32 \
            --enable-jit \
            --enable-unicode
         "
            )
            ->withMakeInstallOptions('install ')
            //->withPkgConfig('/usr/pcre2/lib/pkgconfig')
            ->disableDefaultPkgConfig()
            //->withPkgName("libpcre2-16     libpcre2-32    libpcre2-8      libpcre2-posix")
            ->disablePkgName()
            //->withLdflags('-L/usr/pcre2/lib')
            ->disableDefaultLdflags()
            ->withLicense(
                'https://github.com/PCRE2Project/pcre2/blob/master/COPYING',
                Library::LICENSE_PCRE2
            ) //PCRE2 LICENCE
            ->withHomePage('https://github.com/PCRE2Project/pcre2.git')
    );
}