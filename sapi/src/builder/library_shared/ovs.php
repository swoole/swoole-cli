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
        //->withFile('ovs-v3.2.0.tar.gz')
        //->withAutoUpdateFile()
        ->withFile('ovs-latest.tar.gz')
        ->withDownloadScript(
            'ovs',
            <<<EOF
            git clone -b main --depth=1 --progress https://github.com/openvswitch/ovs.git
            # git clone -b v3.2.0 --depth=1 --progress https://github.com/openvswitch/ovs.git
EOF
        )
        ->withPrefix($ovs_prefix)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add mandoc man-pages
        apk add ghostscript
        pip3 install pipenv
        pip3 install sphinx virtualenv

        # apk add bind-tools  # dig pypi.org

        # sysctl -w net.ipv6.conf.all.disable_ipv6=1
        # sysctl -w net.ipv6.conf.default.disable_ipv6=1
EOF
        )
        ->withPreInstallCommand('debian', <<<EOF
        apt install -y git curl python3 python3-pip python3-dev wget   sudo file
        apt install -y libssl-dev ca-certificates

        apt install -y  \
        git gcc clang make cmake autoconf automake openssl python3 python3-pip  libtool  \
        openssl  curl  libssl-dev  libcap-ng-dev uuid uuid-runtime

        apt install -y kmod iptables
        apt install -y netcat-openbsd
        apt install -y tcpdump nmap traceroute net-tools dnsutils iproute2 procps iputils-ping iputils-arping
        apt install -y conntrack
        apt install -y bridge-utils
        apt install -y libelf-dev  libbpf-dev # libxdp-dev
        apt install -y graphviz
        apt install -y libjemalloc2   libjemalloc-dev  libnuma-dev   libpcap-dev  libunbound-dev  libunwind-dev  llvm-dev
        apt install -y bc init ncat
EOF
        )
        ->withBuildScript(
            <<<EOF
        set -x
        ./boot.sh
        ./configure --help

        ./configure \
        --prefix={$ovs_prefix} \
        --enable-ssl

        make -j {$p->maxJob}

        make install


EOF
        )
        ->withBinPath($ovs_prefix . '/bin/');

    $p->addLibrary($lib);
};
