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
        //->withFile('ovn-v23.03.1.tar.gz')
        //->withAutoUpdateFile()
        ->withFile('ovn-latest.tar.gz')
        ->withDownloadScript(
            'ovn',
            <<<EOF
            git clone -b main --depth=1 --progress https://github.com/ovn-org/ovn.git
EOF
        )
        ->withPrefix($ovn_prefix)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add mandoc man-pages
        apk add ghostscript
        pip3 install pipenv
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


        sh ./boot.sh
        ./configure --help

        ./configure  \
        --prefix={$ovn_prefix} \
        --enable-ssl \
        --with-ovs-source={$workdir}/ovs/ \
        --with-ovs-build={$workdir}/ovs/

        make -j {$p->maxJob}

        make install


EOF
        )
        ->withDependentLibraries('ovs')
        ->withBinPath($ovn_prefix . '/bin/');

    $p->addLibrary($lib);
};
