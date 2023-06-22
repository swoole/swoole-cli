<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libOpenEXR_prefix = LIBOPENEXR_PREFIX;
    $lib = new Library('libOpenEXR');
    $lib->withHomePage('http://www.openexr.com/')
        ->withLicense('https://github.com/AcademySoftwareFoundation/openexr/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://github.com/AcademySoftwareFoundation/openexr/archive/refs/tags/v3.1.5.tar.gz')
        ->withManual('https://github.com/AcademySoftwareFoundation/openexr.git')
        ->withManual('https://openexr.com/en/latest/install.html#install')
        ->withFile('openexr-v3.1.5.tar.gz')
        ->withPrefix($libOpenEXR_prefix)
        //->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libOpenEXR_prefix)
        ->withBuildScript(
            <<<EOF
        # cmake .  -DCMAKE_INSTALL_PREFIX={$libOpenEXR_prefix}

        cmake.   --install-prefix={$libOpenEXR_prefix}
        cmake --build .  --target install --config Release
EOF
        )
        ->withPkgName('Imath OpenEXR')
        ->withBinPath('$libOpenEXR_prefix' . '/bin');

    $p->addLibrary($lib);
};
