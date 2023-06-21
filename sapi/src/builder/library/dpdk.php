<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dpdk_prefix = DPDK_PREFIX;
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
            ninja -C build install
            ldconfig
            pkg-config --modversion libdpdk
EOF
            )
            ->withBinPath($dpdk_prefix . '/bin/')
    );
};
