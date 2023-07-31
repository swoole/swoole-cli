<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('gnulib'))
            ->withHomePage('https://www.gnu.org/software/gnulib/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/gnulib/manual/')
            ->withManual('https://www.gnu.org/software/gnulib/manual/html_node/Building-gnulib.html')
            ->withUrl('https://github.com/coreutils/gnulib/archive/refs/heads/master.zip')
            ->withDownloadScript(
                'gnulib',
                <<<EOF
              git clone -b master --depth 1 --single-branch  https://git.savannah.gnu.org/git/gnulib.git
EOF
            )
            ->withFile('gnulib-latest.tar.gz')
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF

                ./gnulib-tool --help
                exit 3
                gnulib-tool --create-megatestdir --with-tests --dir=...
                ./configure
                make dist
                ./do-autobuild
                             return 0 ;
EOF
            )
            ->withPkgConfig('')
            ->withPkgName('')
    );
};
