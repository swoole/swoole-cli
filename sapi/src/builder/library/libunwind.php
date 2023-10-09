<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libunwind_prefix = LIBUNWIND_PREFIX;
    $lib = new Library('libunwind');
    $lib->withHomePage('https://www.nongnu.org/libunwind/')
        ->withLicense('https://github.com/libunwind/libunwind/blob/master/LICENSE', Library::LICENSE_MIT)
        ->withManual('https://github.com/libunwind/libunwind.git')
        ->withManual('http://www.nongnu.org/libunwind/')
        ->withUrl('http://download.savannah.nongnu.org/releases/libunwind/libunwind-1.6.2.tar.gz')
        ->withConfigure(
            <<<EOF
            autoreconf -i
            ./configure --help

            PACKAGES='zlib liblzma'
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$libunwind_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-coredump \
            --enable-ptrace \
            --enable-setjmp  \
            --enable-minidebuginfo \
            --enable-zlibdebuginfo

EOF
        )
        ->withPkgName('libunwind')
        ->withDependentLibraries('zlib', 'liblzma');
    $p->addLibrary($lib);
};
