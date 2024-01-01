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
            ->withFile('dpdk-v23.11.tar.gz')
            ->withDownloadScript(
                'dpdk-stable',
                <<<EOF
                # https://git.dpdk.org/dpdk-stable/refs

                git clone -b v23.11 --depth=1 https://dpdk.org/git/dpdk-stable
EOF
            )
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
            apk add python3 py3-pip
            # pip3 install  pyelftools -i https://pypi.tuna.tsinghua.edu.cn/simple
            # pip3 install  pyelftools -ihttps://pypi.python.org/simple
            apk add meson
            pip3 install  pyelftools
            apk add bsd-compat-headers
            # apk add libxdp libxdp-static
            # apk add libxdp numactl-dev numactl-tools
            # apk add libfdt
            # apk add libarchive-dev libarchive-static
EOF
            )
            ->withPreInstallCommand(
                'ubuntu',
                <<<EOF
            apt install -y python3-pyelftools
            apt install -y zlib1g-dev
            apt install -y libjansson-dev
            apt install -y nettle-dev
            apt install -y libhogweed6
            apt install -y libnuma-dev
            apt install -y liblz4-dev
            apt install -y libzstd-dev
            apt install -y libarchive-dev
            apt install -y libnl-3-dev libnl-genl-3-dev libnl-nf-3-dev libnl-xfrm-3-dev libnl-route-3-dev libnl-idiag-3-dev
            apt install -y libibumad-dev
            apt install -y librdmacm-dev
            apt install -y libibnetdisc-dev
            apt install -y libibverbs-dev
            apt install -y libibmad-dev
            apt install -y libbsd-dev
            apt install -y libpcap-dev
            apt install -y libelf-dev
            apt install -y libxdp-dev
            apt install -y libbpf-dev
            apt install -y libipsec-mb-dev libipsec-mb1 librte-crypto-ipsec-mb23
            apt install -y libdlpack-dev
            apt install -y libdmlc-dev  #  分布式机器学习库
            apt install -y libfdt-dev
            apt install -y libisal-dev # Intel(R) Intelligent Storage Acceleration Library

            apt install -y python3-sphinx
            apt install -y doxygen
EOF
            )
            ->withCleanBuildDirectory()
            ->withBuildCached(false)
            ->withBuildScript(
                <<<EOF

            test -d build && rm -rf build
            meson  -h
            meson setup -h
            # meson configure -h

            PACKAGES=" jansson  openssl libxml-2.0  nettle hogweed gmp  "
            PACKAGES="\$PACKAGES numa "
            PACKAGES="\$PACKAGES zlib  liblzma liblz4 libzstd "
            # PACKAGES="\$PACKAGES  liblzma  "
            PACKAGES="\$PACKAGES libarchive "
            # PACKAGES="\$PACKAGES libpcap "
            # PACKAGES="\$PACKAGES  libbpf "
            PACKAGES="\$PACKAGES libmlx4 libibverbs libmlx5 libefa libibmad libibnetdisc libibumad libmana librdmacm  "
            PACKAGES="\$PACKAGES libnl-genl-3.0 libnl-idiag-3.0 libnl-route-3.0 libnl-xfrm-3.0 "
            # PACKAGES="\$PACKAGES libbsd libbsd-overlay libmd "


            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) "
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) "

            # CPPFLAGS="\$CPPFLAGS -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include  " # -I{$libarchive_prefix}/include
            # LDFLAGS="\$LDFLAGS -L{$libiconv_prefix}/lib -L{$bzip2_prefix}/lib " # -L{$libarchive_prefix}/lib
            LIBS="\$LIBS  -liconv -lbz2 "

            CPPFLAGS="\$CPPFLAGS" \
            LDFLAGS="\$LDFLAGS" \
            LIBS="\$LIBS"  \
            meson setup  build \
            -Dprefix={$dpdk_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Db_pie=true \
            -Dtests=false \
            -Dexamples=all


            ninja -C build
            ninja -C build install

            # ldconfig
            # pkg-config --modversion libdpdk
EOF
            )
            ->withBinPath($dpdk_prefix . '/bin/')
            /*
            ->withDependentLibraries(
                'jansson',
                'zlib',
                'openssl',
                'libmlx5',
                'libnl',
                'liblzma',
                'liblz4',
                'libiconv',
                'libzstd',
                'bzip2',
                'nettle',
                'bzip2',
                'libxml2',
                'libiconv',
                'gmp',
                'libarchive',
                'numa',
                'libpcap',
                'libxdp',
                'libbpf',
                'libbsd',

            )
            */
            ->withPkgName('libdpdk-libs')
            ->withPkgName('libdpdk')
    );
};

/*

DPDK (Data Plane Development Kit)

PPS（Packet Per Second)

PMD（Poll Mode Driver）

UIO（Userspace I/O）

Zero Copy、无系统调用的好处  零拷贝


https://cloud.tencent.com/developer/article/1198333

https://www.packetcoders.io/what-is-dpdk/

 */
