<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('bzip2'))
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withManual('https://www.sourceware.org/bzip2/docs.html')
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withPrefix($bzip2_prefix)
            ->withMakeOptions('PREFIX=' . $bzip2_prefix)
            ->withMakeInstallOptions('PREFIX=' . $bzip2_prefix)
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
            ->withBinPath($bzip2_prefix . '/bin/')
            ->withScriptAfterInstall(
                <<<EOF
                mkdir -p {$bzip2_prefix}/lib/pkgconfig/
                cat > {$bzip2_prefix}/lib/pkgconfig/bz2.pc <<'__bzip2__EOF'
prefix={$bzip2_prefix}
exec_prefix=\${prefix}
libdir=\${exec_prefix}/lib
includedir=\${prefix}/include

Name: bz2
Description: bzip2 library
Version: 1.0.8

Requires:
Libs: -L\${libdir}  -lbz2
Cflags: -I\${includedir}

__bzip2__EOF
EOF
            )
            //->withPkgName('bz2')
    );
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . BZIP2_PREFIX . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . BZIP2_PREFIX . '/lib');
    $p->withVariable('LIBS', '$LIBS -lbz2');
};
