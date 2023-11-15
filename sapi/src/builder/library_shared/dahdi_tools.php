<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dahdi_tools_prefix = DAHDI_TOOLS_PREFIX;
    $lib = new Library('dahdi_tools');
    $lib->withHomePage('https://www.asterisk.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/asterisk/dahdi-tools.git')
        ->withFile('dahdi-tools-latest.tar.gz')
        ->withDownloadScript(
            'dahdi-tools',
            <<<EOF
                git clone --depth=1 https://github.com/asterisk/dahdi-tools.git
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            # apk add uuid-runtime
EOF
        )
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y libusb-dev  libpcap-dev
EOF
        )
        ->withPrefix($dahdi_tools_prefix)
        ->withBuildScript(
            <<<EOF
        autoreconf -i
        ./configure --help

        ./configure \
        --prefix={$dahdi_tools_prefix} \
        --enable-shared=yes \
        --enable-static=no

        make -j {$p->maxJob}
        make install
        make install-config
EOF
        )
        ->withBinPath($dahdi_tools_prefix . '/sbin/')

    ;

    $p->addLibrary($lib);
};
