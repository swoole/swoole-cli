<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $unix_odbc_prefix = UNIX_ODBC_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $custom_clean_script = '';
    if ($p->isMacos()) {
        $custom_clean_script .= <<<EOF
        sed -i.bak 's@$(top_build_prefix)libltdl/libltdlc.la@@' {$unix_odbc_prefix}/lib/pkgconfig/odbc.pc
        sed -i.bak 's@$(top_build_prefix)libltdl/libltdlc.la@@' {$unix_odbc_prefix}/lib/pkgconfig/odbcinst.pc

EOF;
    }

    $p->addLibrary(
        (new Library('unix_odbc'))
            ->withHomePage('https://github.com/lurcher/unixODBC')
            ->withLicense('https://github.com/lurcher/unixODBC/blob/master/LICENSE', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/lurcher/unixODBC/releases/download/2.3.11/unixODBC-2.3.11.tar.gz')
            ->withFileHash('md5', '0ff1fdbcb4c3c7dc2357f3fd6ba09169')
            ->withPrefix($unix_odbc_prefix)
            ->withconfigure(
                <<<EOF
            aclocal
            autoconf
            autoheader
            automake --add-missing
            ./configure --help

            PACKAGES_NAMES="readline"
            CPPFLAGS="\$(pkg-config --cflags-only-I --static \$PACKAGES_NAMES ) -I{$iconv_prefix}/include" \
            LDFLAGS="\$(pkg-config  --libs-only-L   --static \$PACKAGES_NAMES ) -L{$iconv_prefix}/lib"  \
            LIBS="\$(pkg-config     --libs-only-l   --static \$PACKAGES_NAMES ) -liconv" \
            ./configure \
            --prefix={$unix_odbc_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-readline=yes \
            --enable-editline=no \
            --enable-iconv=yes \
            --enable-threads=yes \
            --enable-gui=no \
            --enable-ltdl-install


EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$unix_odbc_prefix}/lib/*.so.*
            rm -rf {$unix_odbc_prefix}/lib/*.so
            rm -rf {$unix_odbc_prefix}/lib/*.dylib
            {$custom_clean_script}

EOF
            )
            ->withDependentLibraries('readline', 'libiconv')
            ->withBinPath($unix_odbc_prefix . '/bin/')
            ->withPkgName('odbc')
            ->withPkgName('odbccr')
            ->withPkgName('odbcinst')
    );
};
