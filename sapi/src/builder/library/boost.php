<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $boost_prefix = BOOST_PREFIX;
    $icu_prefix = ICU_PREFIX;
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
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($boost_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(<<<EOF
            # 观察使用系统软件包安装结果
            # apk add boost1.80-dev
            # apk add boost1.80-static


            # boost components: filesystem regex system


            ./bootstrap.sh --help
            ./bootstrap.sh --show-libraries

            ./bootstrap.sh \
            --prefix={$boost_prefix} \
            --with-icu={$icu_prefix} \
            --with-toolset={$p->get_C_COMPILER()} \
            --with-libraries=all

            ./b2 --help
            # b2 [options] [properties] [install|stage]
            # -stdlib=libc++
            # -stdlib=libstdc++

            ./b2 \
            --prefix={$boost_prefix} \
            --layout=versioned
            variant=release \
            toolset={$p->get_C_COMPILER()} \
            threading=multi link=static runtime-link=static \
            install

            # headers
            #   --build-type=complete \
            #  cxxflags=" -std=c++11 -stdlib=libstdc++" \
            #  linkflags="-stdlib=libstdc++" \

   EOF
        )
        ->withPkgName('boost')
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
