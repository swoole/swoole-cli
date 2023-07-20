<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $rav1e_prefix = RAV1E_PREFIX;
    $lib = new Library('rav1e');
    $lib->withHomePage('https://github.com/xiph/rav1e.git')
        ->withLicense('https://github.com/xiph/rav1e/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/xiph/rav1e/blob/master/README.md')
        ->withUrl('https://github.com/xiph/rav1e/archive/refs/tags/v0.6.6.tar.gz')
        ->withFile('rav1e-v0.6.6.tar.gz')
        ->withPrefix($rav1e_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($rav1e_prefix)
        ->withBuildScript(
            <<<EOF
        cargo cbuild --release \
        --prefix={$rav1e_prefix} \
        --libdir={$rav1e_prefix}/lib \
        -C link-arg=-lz -vv

        cargo cinstall

EOF
        )
        ->withPkgName('')
        ->withDependentLibraries('common')
    ;

    $p->addLibrary($lib);
};
