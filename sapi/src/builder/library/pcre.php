<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pcre_prefix = PCRE_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('pcre'))
            ->withHomePage('http://www.pcre.org/')
            ->withUrl('https://sourceforge.net/projects/pcre/files/pcre/8.45/pcre-8.45.tar.gz')
            ->withDocumentation('http://www.pcre.org/')
            ->withManual('http://www.pcre.org/')
            ->withLicense(
                'https://github.com/PCRE2Project/pcre2/blob/master/COPYING',
                Library::LICENSE_SPEC
            )
            ->withFile('pcre-8.45.tar.gz')
            ->withPrefix($pcre_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($pcre_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                PACKAGES="readline zlib"
                CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) -I{$bzip2_prefix}/include" \
                LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) -L{$bzip2_prefix}/lib " \
                LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) -lbz2 " \
                ./configure \
                --prefix=$pcre_prefix \
                --enable-shared=no \
                --enable-static=yes \
                --enable-pcre16 \
                --enable-pcre32 \
                --enable-jit \
                --enable-utf \
                --enable-unicode-properties \
                --enable-pcregrep-libz \
                --enable-pcregrep-libbz2 \
                --enable-pcretest-libreadline


 EOF
            )
            ->withBinPath($pcre_prefix . '/bin/')
            ->withPkgName("libpcre")
            ->withPkgName("libpcre16")
            ->withPkgName("libpcre32")
            ->withPkgName("libpcrecpp")
            ->withPkgName("libpcreposix")
            ->depends('readline', 'zlib', 'bzip2')
    );
};
