<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $mpfr_prefix = MPFR_PREFIX;
    $gmp_prefix = GMP_PREFIX;

    //多精度浮点计算的 C 库
    # 多精度复杂算术库
    # 其他计算库 https://www.mpfr.org/

    $lib = new Library('mpfr');
    $lib->withHomePage('https://www.mpfr.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://www.mpfr.org/mpfr-current/mpfr.html#Installing-MPFR')
        ->withFile('ompfr-latest.tar.gz')
        ->withDownloadScript(
            'mpfr',
            <<<EOF
                git clone https://gitlab.inria.fr/mpfr/mpfr.git
EOF
        )
        ->withPreInstallCommand(
            "alpine",
            <<<EOF
        apk add texinfo
EOF
        )
        ->withConfigure(
            <<<EOF
        sh autogen.sh
        ./configure --help


        PACKAGES='gmp  '
        PACKAGES="\$PACKAGES "

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$mpfr_prefix} \
        --enable-shared=no \
        --enable-static=yes

EOF
        )
        ->withPkgName('mpfr')
        ->withDependentLibraries('gmp');

    $p->addLibrary($lib);
};
