<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lib = new Library('firefox');
    $lib->withHomePage('https://www.mozilla.org/en-US/firefox/all/#product-desktop-release')
        ->withLicense('https://www.mozilla.org/en-US/firefox/all/#product-desktop-release', Library::LICENSE_SPEC)
        ->withManual('https://archive.mozilla.org/pub/firefox/releases/')
        ->withDownloadScript(
            'firefox',
            <<<'EOF'
                FIREFOX_VERSION=114.0b9
                DOWNLOAD_FIREFOX_URL_PREFIX=https://archive.mozilla.org/pub/firefox/releases
                DOWNLOAD_FIREFOX_URL=${DOWNLOAD_FIREFOX_URL_PREFIX}/${FIREFOX_VERSION}/linux-${ARCH}/en-US/firefox-${FIREFOX_VERSION}.tar.bz2
                curl -Lo firefox-${FIREFOX_VERSION}.tar.bz2 ${DOWNLOAD_FIREFOX_URL}
                tar -jxvf firefox-${FIREFOX_VERSION}.tar.bz2
EOF
        )
        ->withBuildCached(false)
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withPreInstallCommand(
            'debian',
            <<<EOF

        apt install -y mercurial

EOF
        )

        ->withBuildScript(
            <<<EOF
            mkdir -p build
            cd build

EOF
        )

    ;

    $p->addLibrary($lib);

};
