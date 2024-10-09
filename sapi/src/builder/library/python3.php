<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $python3_prefix = PYTHON3_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;;
    $libintl_prefix = GETTEXT_PREFIX;
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
        ->withInstallCached(false)
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
        CFLAGS="-DOPENSSL_THREADS {$ldflags} -fPIC "
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

        CFLAGS="\$CFLAGS " \
        CPPFLAGS="\$CPPFLAGS " \
        LDFLAGS="\$LDFLAGS  " \
        LIBS="\$LIBS " \
        LINKFORSHARED=" " \
        CCSHARED=" " \
        LDSHARED=" " \
        LDCXXSHARED=" " \
        ./configure \
        --prefix={$python3_prefix} \
        --enable-shared=no \
        --disable-test-modules \
        --with-static-libpython \
        --with-system-expat=yes \
        --with-system-libmpdec=no \
        --with-readline=readline \
        --with-builtin-hashlib-hashes="md5,sha1,sha2,sha3,blake2" \
        --with-openssl={$openssl_prefix} \
        --with-ssl-default-suites=openssl \
        --without-valgrind \
        --without-dtrace \
        --with-ensurepip=install


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

        PYTHONPATH=$({$python3_prefix}/bin/python3 -c "import site, os; print(os.path.join(site.USER_BASE, 'lib', 'python', 'site-packages'))")
        echo \${PYTHONPATH}

        mkdir -p {$python3_prefix}/python_hacl
        cp -rf {$p->getBuildDir()}/python3/Modules/_hacl/* {$python3_prefix}/python_hacl/
EOF
        )
        //->withPkgName('python3')
        //->withPkgName('python3-embed')
        ->withDependentLibraries(
            'zlib',
            'openssl',
            'sqlite3',
            'bzip2',
            'liblzma',
            'readline',
            'ncurses',
            'util_linux',
            'gettext',
            'libexpat',
            'mpdecimal',
            'libb2'
        );

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $python3_prefix . '/python_hacl/');
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $python3_prefix . '/python_hacl/include/');
    # $p->withVariable('LDFLAGS', '$LDFLAGS -l:' . $python3_prefix . '/python_hacl/libHacl_Hash_SHA2.a');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $python3_prefix . '/python_hacl/');
    $p->withVariable('LIBS', '$LIBS -lHacl_Hash_SHA2');

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $python3_prefix . '/include/python3.12/');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $python3_prefix . '/lib/');
    $p->withVariable('LIBS', '$LIBS -lpython3.12');
    if ($p->isMacos()) {
        $p->withVariable('LDFLAGS', '$LDFLAGS -framework CoreFoundation ');
    }

};
# 构建独立版本 python 参考
# https://github.com/indygreg/python-build-standalone.git

# 配置参考 https://docs.python.org/zh-cn/3.12/using/configure.html
# 参考文档： https://wiki.python.org/moin/BuildStatically
