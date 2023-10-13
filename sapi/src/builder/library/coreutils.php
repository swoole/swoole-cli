<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $coreutils_prefix  = COREUTILS_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $gmp_prefix = GMP_PREFIX;
    $libintl_prefix = LIBINTL_PREFIX ;
    $libunistring_prefix = LIBUNISTRING_PREFIX;

    //coreutils    包括常用的命令，如 cat、ls、rm、chmod、mkdir、wc、whoami 和许多其他命令
    $lib = new Library('coreutils');
    $lib->withHomePage('https://www.gnu.org/software/coreutils/')
        ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_GPL)
        ->withManual('https://www.gnu.org/software/coreutils/')
        ->withUrl('https://mirrors.aliyun.com/gnu/coreutils/coreutils-9.1.tar.gz')
        ->withFile('coreutils-9.1.tar.gz')
        ->withHttpProxy(false)
        ->withPrefix($coreutils_prefix)
        ->withCleanBuildDirectory()
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF

                ./bootstrap
                ./configure --help

                FORCE_UNSAFE_CONFIGURE=1 ./configure \
                --prefix={$coreutils_prefix} \
                --with-openssl=yes \
                --with-libiconv-prefix={$iconv_prefix} \
                --with-libgmp-prefix={$gmp_prefix} \
                --without-libintl-prefix

                # --with-libintl-prefix={$libintl_prefix}
                # gettext 包含 libintl

EOF
        )



        ->withPkgName('example')
        ->withBinPath($coreutils_prefix . '/bin/')
        ->withDependentLibraries('libiconv', 'gmp') //,'gettext'

    ;

    $p->addLibrary($lib);


};
