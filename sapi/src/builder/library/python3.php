<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $python3_prefix = PYTHON3_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;;
    $libintl_prefix = GETTEXT_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;

    $static_flag = $p->isMacos() ? '' : ' -static  ';
    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';

    $lib = new Library('python3');
    $lib->withHomePage('https://www.python.org/')
        ->withLicense('https://docs.python.org/3/license.html', Library::LICENSE_LGPL)
        ->withManual('https://www.python.org')
        ->withManual('https://github.com/python/cpython.git')
        ->withUrl('https://www.python.org/ftp/python/3.12.2/Python-3.12.2.tgz')
        //->withUrl('https://www.python.org/ftp/python/3.13.0/Python-3.13.0.tgz')
        //->withUrl('https://github.com/python/cpython/archive/refs/tags/v3.13.0.tar.gz')
        ->withPrefix($python3_prefix)
        ->withBuildCached(false)
        ->withInstallCached(true)
        ->withBuildScript(
            <<<EOF

        ./configure --help

        sed -i.backup 's/py_cv_module__ctypes=yes/py_cv_module__ctypes=disabled/' ./configure
        sed -i.backup 's/py_cv_module_xxlimited=yes/py_cv_module_xxlimited=disabled/' ./configure
        sed -i.backup 's/py_cv_module_xxlimited_35=yes/py_cv_module_xxlimited_35=disabled/' ./configure
        sed -i.backup 's/py_cv_module__scproxy=yes/py_cv_module__scproxy=disabled/' ./configure
        sed -i.backup 's/py_cv_module__tkinter=yes/py_cv_module__tkinter=disabled/' ./configure

        PACKAGES='  '
        PACKAGES="\$PACKAGES libmpdec"
        PACKAGES="\$PACKAGES libb2"
        PACKAGES="\$PACKAGES zlib"
        PACKAGES="\$PACKAGES liblzma"
        PACKAGES="\$PACKAGES ncursesw panelw formw menuw ticw"
        PACKAGES="\$PACKAGES readline"
        PACKAGES="\$PACKAGES uuid"
        PACKAGES="\$PACKAGES expat"
        PACKAGES="\$PACKAGES openssl"
        PACKAGES="\$PACKAGES sqlite3"

        # -Wl,–no-export-dynamic
        CFLAGS="-DOPENSSL_THREADS {$static_flag}  -fPIC -DCONFIG_64=1"
        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)  {$static_flag}  "
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)  {$static_flag}  "
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
        CFLAGSFORSHARED="" CCSHARED="" LDSHARED="" LDCXXSHARED="" LINKFORSHARED="" \
        MODULE_BUILDTYPE=static \
        ./configure \
        --prefix={$python3_prefix} \
        --enable-shared=no \
        --disable-test-modules \
        --with-static-libpython \
        --with-system-expat=yes \
        --with-system-libmpdec=yes \
        --with-readline=readline \
        --with-openssl={$openssl_prefix} \
        --with-ssl-default-suites=openssl \
        --with-builtin-hashlib-hashes=md5,sha1,sha2,sha3,blake2 \
        --without-valgrind \
        --without-dtrace \
        --with-ensurepip=install


        # 只能动态构建的扩展 请查看 Modules/Setup.stdlib 描述,找到并注释
        # ctypes 、xxlimited 、scproxy（onliy macos)  、tkinter
        # 注释方法： sed -i 's/^pattern/;\1/' file.txt
        # \1 表示匹配到的内容

        # sed -i.backup "s/^\*shared\*/\*static\*/g" Modules/Setup.stdlib

        # sed -i.backup 's/^_ctypes _ctypes\/_ctypes\.c/# \1/' Modules/Setup.stdlib
        # sed -i.backup 's/^_scproxy _scproxy\.c/# \1/' Modules/Setup.stdlib

        # sed -i.backup 's/^xxlimited xxlimited\.c/# \1/' Modules/Setup.stdlib
        # sed -i.backup 's/^xxlimited_35 xxlimited_35\.c/# \1/' Modules/Setup.stdlib

        cp -f Modules/Setup.stdlib  Modules/Setup.local

        CFLAGS="\$CFLAGS " \
        CPPFLAGS="\$CPPFLAGS " \
        LDFLAGS="\$LDFLAGS  " \
        LIBS="\$LIBS " \
        CFLAGSFORSHARED="" CCSHARED="" LDSHARED="" LDCXXSHARED="" LINKFORSHARED="" \
        MODULE_BUILDTYPE=static \
        make -j {$p->getMaxJob()}

        make install

        mkdir -p {$python3_prefix}/_hacl/include/
        cp -f Modules/_hacl/libHacl_Hash_SHA2.a   {$python3_prefix}/lib/
        cp -f Modules/_hacl/*.h  {$python3_prefix}/_hacl/include/

        {$python3_prefix}/bin/python3 -E -c 'import sys ; from sysconfig import get_platform ; print("%s-%d.%d" % (get_platform(), *sys.version_info[:2])) ; '
        {$python3_prefix}/bin/python3 -E -c 'import sys ; print(sys.modules) ; '
        {$python3_prefix}/bin/python3 -E -c 'import sys ; print(dir(sys)) ; '
        {$python3_prefix}/bin/python3-config --cflags
        {$python3_prefix}/bin/python3-config --ldflags
        {$python3_prefix}/bin/python3-config --libs

        PYTHONPATH=$({$python3_prefix}/bin/python3 -c "import site, os; print(os.path.join(site.USER_BASE, 'lib', 'python', 'site-packages'))")
        echo \${PYTHONPATH}

        # PYTHONPATH={$p->getGlobalPrefix()}/bin/python3/bin/
        # PYTHONHOME=/custom/output

EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            sed -i.backup "s/-ldl/  /g" {$python3_prefix}/lib/pkgconfig/python3.pc
            sed -i.backup "s/-ldl/  /g" {$python3_prefix}/lib/pkgconfig/python3-embed.pc

EOF
        )
        ->withPkgName('python3-embed')
        ->withPkgName('python3')
        ->withDependentLibraries(
            'libmpdecimal',
            'libb2',
            'readline',
            'ncurses',
            'libexpat',
            'zlib',
            'openssl',
            'sqlite3',
            'bzip2',
            'liblzma',
            'util_linux',
            'gettext'
        );

    $p->addLibrary($lib);

    if ($p->isMacos()) {
        $p->withVariable('LDFLAGS', '$LDFLAGS -framework CoreFoundation ');

        //module  _scproxy needs SystemConfiguration and CoreFoundation framework
        //$p->withVariable('LDFLAGS', '$LDFLAGS -framework SystemConfiguration -framework CoreFoundation ');
    }
    //libHacl_Hash_SHA2.a
    $p->withVariable('LIBS', '$LIBS -lHacl_Hash_SHA2');
    $p->withVariable('CPPFLAGS', "\$CPPFLAGS  -I{$python3_prefix}/_hacl/include/");
};
# 构建独立版本 python 参考
# https://github.com/indygreg/python-build-standalone.git
# 参考文档： https://wiki.python.org/moin/BuildStatically
# https://knazarov.com/posts/statically_linked_python_interpreter/


# 配置参考 https://docs.python.org/zh-cn/3.12/using/configure.html

# https://github.com/python/cpython
