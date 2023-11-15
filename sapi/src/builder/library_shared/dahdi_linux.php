<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dahdi_linux_prefix = DAHDI_LINUX_PREFIX;
    $lib = new Library('dahdi_linux');
    $lib->withHomePage('https://www.asterisk.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/asterisk/dahdi-linux.git')
        ->withFile('dahdi-linux-latest.tar.gz')
        ->withDownloadScript(
            'dahdi-linux',
            <<<EOF
        git clone  --depth=1 https://github.com/asterisk/dahdi-linux.git
EOF
        )
        ->withPrefix($dahdi_linux_prefix)
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y linux-generic
        apt install -y linux-image-$(uname -r)


        release=$(linux-version list | grep -e '-generic$' | sort -V | tail -n1)
        GRUB_DEFAULT="Advanced options for Ubuntu>Ubuntu, with Linux \$release"
        # sudo update-grub

EOF
        )

        ->withBuildScript(
            <<<EOF

        make -j {$p->maxJob}
        make install DESTDIR={$dahdi_linux_prefix}
EOF
        )
    ;

    $p->addLibrary($lib);
};
