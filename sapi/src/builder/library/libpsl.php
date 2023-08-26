<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libpsl_prefix = LIBPSL_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $lib = new Library('libpsl');
    $lib->withHomePage('https://rockdaboot.github.io/libpsl/')
        ->withLicense('https://github.com/rockdaboot/libpsl/blob/master/LICENSE', Library::LICENSE_MIT)
        ->withManual('https://github.com/rockdaboot/libpsl.git')
        ->withFile('libpsl-latest.tar.gz')
        ->withDownloadScript(
            'libpsl',
            <<<EOF
        git clone -b master --depth=1 https://github.com/rockdaboot/libpsl.git
EOF
        )
        ->withPrefix($libpsl_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withBuildScript(
            <<<EOF

                mkdir -p {$libiconv_prefix}/lib/pkgconfig/
                cat > {$libiconv_prefix}/lib/pkgconfig/iconv.pc <<'__libiconv__EOF'
prefix={$libiconv_prefix}
exec_prefix=\${prefix}
libdir=\${exec_prefix}/lib
includedir=\${prefix}/include

Name: iconv
Description: iconv library
Version: 1.16

Requires:
Libs: -L\${libdir} -liconv
Cflags: -I\${includedir}

__libiconv__EOF

                mkdir -p {$libunistring_prefix}/lib/pkgconfig/
                cat > {$libunistring_prefix}/lib/pkgconfig/libunistring.pc <<'__libunistring__EOF'
prefix={$libunistring_prefix}
exec_prefix=\${prefix}
libdir=\${exec_prefix}/lib
includedir=\${prefix}/include

Name: unistring
Description: unistring library
Version: 1.1

Requires:
Libs: -L\${libdir} -lunistring
Cflags: -I\${includedir}

__libunistring__EOF



            meson  -h
            meson setup -h
            # meson configure -h
            test -d build && rm -rf build


            meson setup  build \
            -Dprefix={$libpsl_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dtests=false



            ninja -C build
            ninja -C build install
EOF
        )
        ->withDependentLibraries('libidn2', 'libunistring','libiconv')
    ;

    $p->addLibrary($lib);
};
