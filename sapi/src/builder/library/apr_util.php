<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = APR_UTIL_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $sqlite3_prefix = SQLITE3_PREFIX;
    $pgsql_prefix = PGSQL_PREFIX;
    $apr_prefix = APR_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $libexpat_prefix = LIBEXPAT_PREFIX;
    $lib = new Library('apr_util');
    $lib->withHomePage('https://apr.apache.org/')
        ->withLicense('https://www.apache.org/licenses/', Library::LICENSE_APACHE2)
        ->withUrl('https://dlcdn.apache.org//apr/apr-util-1.6.3.tar.gz')
        ->withManual('https://apr.apache.org/compiling_unix.html')
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF

            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"
            PACKAGES="\$PACKAGES odbc"
            PACKAGES="\$PACKAGES libpq"
            PACKAGES="\$PACKAGES sqlite3"
            PACKAGES="\$PACKAGES apr-1"
            PACKAGES="\$PACKAGES expat"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$libexpat_prefix}/include " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --with-crypto \
            --with-openssl={$openssl_prefix} \
            --with-pgsql={$pgsql_prefix} \
            --with-sqlite3={$sqlite3_prefix} \
            --with-odbc \
            --with-iconv={$libiconv_prefix} \
            --with-apr={$apr_prefix}
EOF
        )
        ->withPkgName('ssl')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries(
            'openssl',
            'zlib',
            'unixODBC',
            "libiconv",
            "apr",
            "sqlite3",
            "pgsql",
            "libexpat"
        );

    $p->addLibrary($lib);

};
