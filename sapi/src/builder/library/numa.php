<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $numa_prefix = NUMA_PREFIX;
    $lib = new Library('numa');
    $lib->withHomePage('https://github.com/numactl/numactl.git')
        ->withLicense('https://github.com/numactl/numactl/blob/master/LICENSE.GPL2', Library::LICENSE_GPL)
        ->withUrl('https://github.com/numactl/numactl/archive/refs/tags/v2.0.16.tar.gz')
        ->withFile('numa-v2.0.16.tar.gz')
        ->withManual('https://github.com/numactl/numactl/blob/master/INSTALL.md')
        ->withPrefix($numa_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($numa_prefix)
        ->withConfigure(
            <<<EOF
            ./autogen.sh
            ./configure --help

            ./configure \
            --prefix={$numa_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('numa')
        ->withBinPath($numa_prefix . '/bin/');

    $p->addLibrary($lib);
};
