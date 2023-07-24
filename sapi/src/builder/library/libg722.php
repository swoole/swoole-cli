<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libg722_prefix = LIBG722_PREFIX;
    $lib = new Library('libg722');
    $lib->withHomePage('https://github.com/sippy/libg722.git')
        ->withLicense('https://github.com/sippy/libg722/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/sippy/libg722.git')
        ->withManual('https://www.itu.int/rec/T-REC-G.722-201209-I/en')
        ->withFile('libg722-v_1_0_3.tar.gz')
        ->withDownloadScript(
            'libg722',
            <<<EOF
            git clone -b v_1_0_3  --depth=1 https://github.com/sippy/libg722.git
EOF
        )
        ->withPrefix($libg722_prefix)
        ->withMakeInstallOptions("DESTDIR={$libg722_prefix} LIBDIR=/lib INCLUDEDIR=/include")
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libg722_prefix}/lib/*.so.*
            rm -rf {$libg722_prefix}/lib/*.so
            rm -rf {$libg722_prefix}/lib/*.dylib
EOF
        );

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libg722_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libg722_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lg722 ');
};
