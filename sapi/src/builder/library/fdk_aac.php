<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $fdk_aac_prefix = FDK_AAC_PREFIX;
    $lib = new Library('fdk_aac');
    $lib->withHomePage('https://sourceforge.net/projects/opencore-amr/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/mstorsjo/fdk-aac.git')
        ->withFile('fdk-aac-latest.tar.gz')
        ->withDownloadScript(
            'fdk-aac',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/mstorsjo/fdk-aac.git
EOF
        )
        ->withPrefix($fdk_aac_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help
            ./configure \
            --prefix={$fdk_aac_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-pic \
            --disable-example

EOF
        )
        ->withPkgName('fdk-aac')
        ->withBinPath($fdk_aac_prefix . '/bin/');
    $p->addLibrary($lib);
};
