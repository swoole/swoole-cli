<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opengl_prefix = OPENGL_PREFIX;
    $lib = new Library('opengl');
    $lib->withHomePage('https://www.opengl.org/')
        ->withLicense('', Library::LICENSE_SPEC)
        ->withFile('opengl-latest.tar.gz')
        ->withDownloadScript(
            'opengl',
            <<<EOF
EOF
        )
        ->withPrefix($opengl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($opengl_prefix)
        ->withBuildScript(
            <<<EOF

EOF
        );


    $p->addLibrary($lib);
};
