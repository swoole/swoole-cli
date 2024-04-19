<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $python3_prefix = PYTHON3_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;;
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
        PACKAGES="\$PACKAGES ncursesw panelw formw menuw ticw"
        PACKAGES="\$PACKAGES readline"
        PACKAGES="\$PACKAGES uuid"
        PACKAGES="\$PACKAGES expat"
        PACKAGES="\$PACKAGES libmpdec"
        PACKAGES="\$PACKAGES libb2"

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

        export CFLAGS="\$CFLAGS "
        export CPPFLAGS="\$CPPFLAGS "
        export LDFLAGS="\$LDFLAGS "
        export LIBS="\$LIBS "
        export LINKFORSHARED=" "

        export CCSHARED=""
        export LDSHARED=""
        export LDCXXSHARED=""

        export LIBLZMA_CFLAGS="\$(pkg-config  --cflags --static liblzma)"
        export LIBLZMA_LIBS="\$(pkg-config    --libs   --static liblzma)"

        export CURSES_CFLAGS="\$(pkg-config  --cflags --static ncursesw)"
        export CURSES_LIBS="\$(pkg-config    --libs   --static ncursesw)"

        export PANEL_CFLAGS="\$(pkg-config  --cflags --static panelw)"
        export PANEL_LIBS="\$(pkg-config    --libs   --static panelw)"

        export LIBMPDEC_CFLAGS="\$(pkg-config  --cflags --static libmpdec)"
        export LIBMPDEC_LDFLAGS="\$(pkg-config    --libs   --static libmpdec)"

        export LIBEXPAT_CFLAGS="\$(pkg-config  --cflags --static expat)"
        export LIBEXPAT_LDFLAGS="\$(pkg-config    --libs   --static expat)"

        export OPENSSL_LDFLAGS="\$(pkg-config     --libs-only-L     --static openssl)"
        export OPENSSL_LIBS="\$(pkg-config        --libs-only-l     --static openssl)"
        export OPENSSL_INCLUDES="\$(pkg-config    --cflags-only-I   --static openssl)"

        export LIBB2_CFLAGS="\$(pkg-config  --cflags --static libb2)"
        export LIBB2_LIBS="\$(pkg-config    --libs   --static libb2)"

        ./configure \
        --prefix={$python3_prefix} \
        --enable-shared=no \
        --disable-test-modules \
        --with-static-libpython \
        --with-system-expat=yes \
        --with-system-libmpdec=yes \
        --with-readline=readline \
        --with-builtin-hashlib-hashes="md5,sha1,sha2,sha3,blake2" \
        --with-openssl={$openssl_prefix} \
        --with-ssl-default-suites=openssl \
        --without-valgrind \
        --without-dtrace

        # --with-libs='expat libmpdec openssl zlib sqlite3 liblzma ncursesw panelw formw menuw ticw readline uuid '
        # --enable-optimizations \
        # --without-system-ffi \

        # echo '*static*' >> Modules/Setup.local

        sed -i.bak "s/^\*shared\*/\*static\*/g" Modules/Setup.stdlib
        cat Modules/Setup.stdlib > Modules/Setup.local

        # make -j {$p->getMaxJob()} LDFLAGS="\$LDFLAGS " LINKFORSHARED=" "
        make -j {$p->getMaxJob()}

        make install

        {$python3_prefix}/bin/python3 -E -c 'import sys ; from sysconfig import get_platform ; print("%s-%d.%d" % (get_platform(), *sys.version_info[:2])) ; '
        {$python3_prefix}/bin/python3 -E -c 'import sys ; print(sys.modules) ; '
        {$python3_prefix}/bin/python3 -E -c 'import sys ; print(dir(sys)) ; '
        {$python3_prefix}/bin/python3-config --cflags
        {$python3_prefix}/bin/python3-config --ldflags
        {$python3_prefix}/bin/python3-config --libs


        mkdir -p {$python3_prefix}/python_hacl
        cp -rf {$p->getBuildDir()}/python3/Modules/_hacl/* {$python3_prefix}/python_hacl/


        unset CFLAGS
        unset CPPFLAGS
        unset LDFLAGS
        unset LIBS
        unset LINKFORSHARED

        unset CCSHARED
        unset LDSHARED
        unset LDCXXSHARED

        unset LIBLZMA_CFLAGS
        unset LIBLZMA_LIBS

        unset CURSES_CFLAGS
        unset CURSES_LIBS

        unset PANEL_CFLAGS
        unset PANEL_LIBS

        unset LIBMPDEC_CFLAGS
        unset LIBMPDEC_LDFLAGS

        unset LIBEXPAT_CFLAGS
        unset LIBEXPAT_LDFLAGS

        unset OPENSSL_LDFLAGS
        unset OPENSSL_LIBS
        unset OPENSSL_INCLUDES

        unset LIBB2_CFLAGS
        unset LIBB2_LIBS


EOF
        )
        //->withPkgName('python3')
        //->withPkgName('python3-embed')
        //->withBinPath($python3_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl', 'sqlite3', 'bzip2', 'liblzma', 'readline', 'ncurses', 'libuuid', 'libintl', 'libexpat', 'mpdecimal', 'libb2');

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $python3_prefix . '/python_hacl/');
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $python3_prefix . '/python_hacl/include/');
    # $p->withVariable('LDFLAGS', '$LDFLAGS -l:' . $python3_prefix . '/python_hacl/libHacl_Hash_SHA2.a');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $python3_prefix . '/python_hacl/');
    $p->withVariable('LIBS', '$LIBS -lHacl_Hash_SHA2');

};
# 构建独立版本 python 参考
# https://github.com/indygreg/python-build-standalone.git

# 配置参考 https://docs.python.org/zh-cn/3.12/using/configure.html
# 参考文档： https://wiki.python.org/moin/BuildStatically
