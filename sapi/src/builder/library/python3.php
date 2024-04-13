<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $python3_prefix = PYTHON3_PREFIX;
    $libintl_prefix = LIBINTL_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;

    $ldflags = $p->isMacos() ? '' : ' -static  ';
    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';

    $lib = new Library('python3');
    $lib->withHomePage('https://www.python.org/')
        ->withLicense('https://docs.python.org/3/license.html', Library::LICENSE_LGPL)
        ->withManual('https://www.python.org')
        //->withUrl('https://www.python.org/ftp/python/3.11.8/Python-3.11.8.tgz')
        ->withUrl('https://www.python.org/ftp/python/3.12.2/Python-3.12.2.tgz')
        ->withPrefix($python3_prefix)
        ->withBuildCached(false)
        //->withInstallCached(false)
        ->withBuildScript(
            <<<EOF

        ./configure --help


        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES zlib"
        PACKAGES="\$PACKAGES sqlite3"
        PACKAGES="\$PACKAGES liblzma"
        PACKAGES="\$PACKAGES ncursesw"
        PACKAGES="\$PACKAGES readline"
        PACKAGES="\$PACKAGES uuid"

        # -Wl,–no-export-dynamic
        CFLAGS="-DOPENSSL_THREADS {$ldflags}  "
        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)  {$ldflags}  "
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)   {$ldflags} -DOPENSSL_THREADS  "
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)  {$libs}"

        CPPFLAGS=" \$CPPFLAGS -I{$bzip2_prefix}/include/ "
        LDFLAGS=" \$LDFLAGS -L{$bzip2_prefix}/lib/ "
        LIBS=" \$LIBS -lbz2 "

        CPPFLAGS=" \$CPPFLAGS -I{$libintl_prefix}/include/ "
        LDFLAGS=" \$LDFLAGS -L{$libintl_prefix}/lib/ "
        LIBS=" \$LIBS -lintl "

        CPPFLAGS=" \$CPPFLAGS -I{$libiconv_prefix}/include/ "
        LDFLAGS=" \$LDFLAGS -L{$libiconv_prefix}/lib/ "
        LIBS=" \$LIBS -liconv "

        echo \$CFLAGS
        echo \$CPPFLAGS
        echo \$LDFLAGS
        echo \$LIBS

        CFLAGS=\$CFLAGS \
        CPPFLAGS="\$CPPFLAGS " \
        LDFLAGS="\$LDFLAGS " \
        LIBS="\$LIBS" \
        LINKFORSHARED=" " \
        ./configure \
        --prefix={$python3_prefix} \
        --enable-shared=no \
        --disable-shared \
        --without-system-expat \
        --without-system-libmpdec \
        --disable-test-modules \
        --with-static-libpython


        # --enable-optimizations \
        # --without-system-ffi \
        # 参考文档： https://wiki.python.org/moin/BuildStatically
        # echo '*static*' >> Modules/Setup.local

        sed -i.bak "s/^\*shared\*/\*static\*/g" Modules/Setup.stdlib
        cat Modules/Setup.stdlib > Modules/Setup.local

        make -j {$p->getMaxJob()} LDFLAGS="\$LDFLAGS " LINKFORSHARED=" "

        make install
EOF
        )
        ->withPkgName('python3')
        ->withPkgName('python3-embed')
        ->withBinPath($python3_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl', 'sqlite3', 'bzip2', 'liblzma', 'readline', 'ncurses', 'libuuid', 'libintl');

    $p->addLibrary($lib);

};
