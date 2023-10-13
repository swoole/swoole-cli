<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $boost_prefix = BOOST_PREFIX;
    $icu_prefix = ICU_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;



    $lib = new Library('boost');
    $lib->withHomePage('https://www.boost.org/')
        ->withLicense('https://www.boost.org/users/license.html', Library::LICENSE_SPEC)
        ->withUrl('https://boostorg.jfrog.io/artifactory/main/release/1.83.0/source/boost_1_83_0.tar.gz')
        ->withManual('https://www.boost.org/doc/libs/1_81_0/more/getting_started/index.html')
        ->withManual('https://github.com/boostorg/wiki/wiki/Getting-Started%3A-Overview')
        ->withManual('https://github.com/boostorg/wiki/wiki/')
        ->withManual('https://www.boost.org/build/')
        ->withManual('https://www.boost.org/build/doc/html/index.html')
        ->withManual('https://www.boost.org/doc/libs/1_83_0/more/getting_started/windows.html')
        ->withManual('https://www.boost.org/doc/libs/1_83_0/more/getting_started/unix-variants.html')

        ->withPrefix($boost_prefix)
        //->withCleanBuildDirectory()
        //->withCleanPreInstallDirectory($boost_prefix)
        //->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
            # 观察使用系统软件包安装结果
            # apk add boost1.80-dev
            # apk add boost1.80-static

            X_CPPFLAGS=""
            if [ "\$OS_RELEASE" = 'alpine' ]; then
                X_CPPFLAGS="-I/usr/include/c++/12.2.1 -I/usr/include/c++/12.2.1/x86_64-alpine-linux-musl"
            elif [ "\$OS_RELEASE" = 'debian' ]; then
                X_CPPFLAGS="-I/usr/include/c++/12 -I/usr/include/x86_64-linux-gnu/c++/12/"
            elif [ "\$OS_RELEASE" = 'ubuntu' ]; then
                X_CPPFLAGS="-I/usr/include/c++/11 -I/usr/include/x86_64-linux-gnu/c++/11/"
            else
                X_CPPFLAGS=""
            fi


            PACKAGES='liblzma libzstd icu-i18n icu-io icu-uc'
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$bzip2_prefix}/inlcude  -I{$libiconv_prefix}/include"
            CPPFLAGS="\$CPPFLAGS \$X_CPPFLAGS "


            # boost components: filesystem regex system


            ./bootstrap.sh --help
            ./bootstrap.sh --with-toolset={$p->get_C_COMPILER()} --show-libraries

            ./bootstrap.sh \
            --prefix={$boost_prefix} \
            --with-icu={$icu_prefix} \
            --with-toolset={$p->get_C_COMPILER()} \
            --with-libraries=all

            ./b2 --help --with-toolset={$p->get_C_COMPILER()}

            # b2 [options] [properties] [install|stage]
            # -stdlib=libc++
            # -stdlib=libstdc++

            ./b2 \
            --prefix={$boost_prefix} \
            --layout=versioned \
            --without-python \
            --without-graph_parallel \
            variant=release \
            toolset={$p->get_C_COMPILER()} \
            threading=multi link=static  \
            cxxflags="-std=c++14   \$CPPFLAGS " \
            linkflags="-stdlib=libstdc++ " \
            release \
            install

            #  headers
            #  runtime-link=static
            #  --build-type=complete \
            #  cxxflags=" -std=c++11 -stdlib=libstdc++" \
            #  linkflags="-stdlib=libstdc++" \

   EOF
        )
        ->withDependentLibraries(
            'zlib',
            'bzip2',
            'liblzma',
            'libzstd',
            'icu',
            'libiconv',
        )

    ;

    $p->addLibrary($lib);
};

/*
Boost libraries

- atomic
- chrono
- container
- context
- contract
- coroutine
- date_time
- exception
- fiber
- filesystem
- graph
- graph_parallel
- headers
- iostreams
- json
- locale
- log
- math
- mpi
- nowide
- program_options
- python
- random
- regex
- serialization
- stacktrace
- system
- test
- thread
- timer
- type_erasure
- url
- wave

 */


/*

  查找标准头位置
  clang++ -v -xc++ -

*/
