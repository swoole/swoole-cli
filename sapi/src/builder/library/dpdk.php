<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dpdk_prefix = DPDK_PREFIX;
    $libarchive_prefix = LIBARCHIVE_PREFIX;
    $numa_prefix = NUMA_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('dpdk'))
            ->withHomePage('http://core.dpdk.org/')
            ->withLicense('https://core.dpdk.org/contribute/', Library::LICENSE_BSD)
            ->withManual('https://github.com/DPDK/dpdk.git')
            ->withManual('http://core.dpdk.org/doc/')
            ->withManual('https://core.dpdk.org/doc/quick-start/')
            ->withFile('dpdk-v22.11.3.tar.gz')
            ->withDownloadScript(
                'dpdk-stable',
                <<<EOF
                # https://git.dpdk.org/dpdk-stable/refs

                git clone -b v22.11.3 --depth=1 https://dpdk.org/git/dpdk-stable
EOF
            )

            ->withPreInstallCommand(
                'alpine',
                <<<EOF
            apk add python3 py3-pip
            # pip3 install meson pyelftools -i https://pypi.tuna.tsinghua.edu.cn/simple
            # pip3 install meson pyelftools -ihttps://pypi.python.org/simple
            pip3 install meson pyelftools
            apk add bsd-compat-headers
EOF
            )
            ->withPreInstallCommand(
                'debian',
                <<<EOF
            apt install python3-pyelftools
EOF
            )
            ->withCleanBuildDirectory()
            ->withBuildLibraryCached(false)
            ->withBuildScript(
                <<<EOF

            test -d build && rm -rf build
            meson  -h
            meson setup -h
            # meson configure -h

            PACKAGES=" jansson  openssl libxml-2.0  nettle hogweed gmp numa "
            PACKAGES=" zlib libarchive liblzma liblz4 libzstd "
            PACKAGES=" libpcap libbpf "
            PACKAGES=" libmlx4 libibverbs libmlx5 libefa libibmad libibnetdisc libibumad libmana librdmacm libnl-3.0 libnl-genl-3.0 libnl-idiag-3.0 libnl-route-3.0 libnl-xfrm-3.0 "
            # PACKAGES=" libbsd libbsd-overlay libmd "


            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) "
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) "

            CPPFLAGS="\$CPPFLAGS -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include "
            LDFLAGS="\$LDFLAGS -L{$libiconv_prefix}/lib -L{$bzip2_prefix}/lib "
            LIBS="\$LIBS  -liconv -lbz2 "

            CPPFLAGS="\$CPPFLAGS" \
            LDFLAGS="\$LDFLAGS" \
            LIBS="\$LIBS"  \
            meson setup  build \
            -Dprefix={$dpdk_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dibverbs_link=static \
            -Dtests=false \
            -Dexamples=''

            # -Dexamples=all
            # -Dexamples=''


            ninja -C build
            ninja -C build install

            # ldconfig
            # pkg-config --modversion libdpdk
EOF
            )
            ->withBinPath($dpdk_prefix . '/bin/')
            ->withDependentLibraries(
                'jansson',
                'zlib',
                'libarchive',
                'numa',
                'libpcap',
                //'libxdp',
                'libbpf',
                'libmlx5',
                //'libbsd',
                'openssl'
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$dpdk_prefix}/lib/*.so.*
            rm -rf {$dpdk_prefix}/lib/*.so
            rm -rf {$dpdk_prefix}/lib/*.dylib
EOF
            )
    );
};

/*

DPDK (Data Plane Development Kit)

PPS（Packet Per Second)

PMD（Poll Mode Driver）

UIO（Userspace I/O）

Zero Copy、无系统调用的好处


https://cloud.tencent.com/developer/article/1198333

https://www.packetcoders.io/what-is-dpdk/

 */
