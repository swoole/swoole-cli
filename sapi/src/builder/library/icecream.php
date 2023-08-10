<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $icecream_prefix = ICECREAM_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $lzo_prefix = LZO_PREFIX;
    $lib = new Library('icecream');
    $lib->withHomePage('https://github.com/icecc/icecream.git')
        ->withLicense('https://github.com/icecc/icecream/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/icecc/icecream#installation')
        ->withFile('icecream-latest.tar.gz')
        ->withDownloadScript(
            'icecream',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/icecc/icecream.git
EOF
        )
        ->withPrefix($icecream_prefix)
        ->withConfigure(
            <<<EOF
            # services/util.h # 25行
            sed -i 's@#include <sys/poll\.h>@#include <poll\.h>@' services/util.h



            sh autogen.sh
            ./configure --help

            PACKAGES='libcap-ng lzo2 libzstd libarchive'
            PACKAGES="\$PACKAGES openssl gmp libxml-2.0 liblz4 liblzma zlib libzstd nettle"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$bzip2_prefix}/include -I{$libiconv_prefix}/include -I{$bzip2_prefix}/include -I{$libxml2_prefix}/include -I{$lzo_prefix}/include" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$bzip2_prefix}/lib -L{$libiconv_prefix}/lib" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -lbz2 -liconv " \
            ./configure \
            --prefix={$icecream_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-libcap_ng \
            --without-man


EOF
        )
        ->withPkgName('icecc')
        ->withBinPath($icecream_prefix . '/bin/:' . $icecream_prefix . '/sbin/')
        ->withDependentLibraries(
            'libcap_ng',
            'lzo',
            'libzstd',
            'libarchive',
            'openssl',
            'libxml2',
            'zlib',
            'liblzma',
            'liblz4',
            'libiconv',
            'libzstd',
            'bzip2',
            'nettle',
            'bzip2',
            'libiconv'
        );


    $p->addLibrary($lib);
};
