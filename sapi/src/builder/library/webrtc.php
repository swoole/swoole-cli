<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = OPENCV_PREFIX;
    $lib = new Library('webrtc');
    $lib->withHomePage('https://webrtc.googlesource.com/src')
        ->withLicense('https://webrtc.googlesource.com/src/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://webrtc.googlesource.com/src')
        ->withDownloadScript(
            'webrtc',
            <<<EOF
               git clone -b main --depth=1 https://webrtc.googlesource.com/src webrtc
EOF
        )
        ->withBuildLibraryCached(false)
        ->withHttpProxy()
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)

        ->withBuildScript(
            <<<EOF
            mkdir -p build
             cd build

EOF
        )

    ;

    $p->addLibrary($lib);

};
