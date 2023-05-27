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
        ->withPkgName('libofproto')
        ->withPkgName('libopenvswitch')
        ->withPkgName('libovsdb')
        ->withPkgName('libsflow')
        ->withBinPath($ovs_prefix . '/bin/');

    $p->addLibrary($lib);
}

function install_ovn(Preprocessor $p)
{
    $workdir = $p->getBuildDir();
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

function install_nginx($p)
{

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
            ->withConfigure(
                <<<EOF
                           apk add python3 py3-pip
            pip3 install meson pyelftools -i https://pypi.tuna.tsinghua.edu.cn/simple
            # pip3 install meson pyelftools -ihttps://pypi.python.org/simple
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
            ->withFile('')
            ->withManual('https://github.com/xdp-project/xdp-tutorial')
            ->withDownloadScript(
                'xdp-tutorial',
                <<<EOF
https://github.com/xdp-project/xdp-tutorial.git
EOF
            )
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
 apk add llvm bpftool
EOF
            )
            ->withBinPath($xdp_prefix . '/bin/')
            ->withSkipBuildInstall()
    );
}
