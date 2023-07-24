<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = OPENCV_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('dahdi-linux');
    $lib->withHomePage('https://www.asterisk.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/asterisk/dahdi-linux.git')
        ->withFile('dahdi-linux-latest.tar.gz')
        ->withDownloadScript(
            'dahdi-linux',
            <<<EOF
                git clone  --depth=1 https://github.com/asterisk/dahdi-linux.git
EOF
        )
        ->withBuildLibraryCached(false)
        ->withHttpProxy()
        ->withPreInstallCommand(
            <<<EOF
            # apk add uuid-runtime
EOF
        )
        ->withSkipDownload()
        ->withUntarArchiveCommand('xz')
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildScript(
            <<<EOF
test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf

mkdir -p /etc/apt/apt.conf.d/

cat > /etc/apt/apt.conf.d/proxy.conf <<'--EOF--'
Acquire::http::Proxy "{$p->getHttpProxy()}";
Acquire::https::Proxy "{$p->getHttpProxy()}";

--EOF--

        apt install -y private package
EOF
        )
        ->withBuildScript(
            <<<EOF
            mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$example_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DOpenSSL_ROOT={$openssl_prefix} \

            # -DCMAKE_CXX_STANDARD=14
            # -DCMAKE_C_COMPILER=clang \
            # -DCMAKE_CXX_COMPILER=clang++ \
            # -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \

            # -DCMAKE_CXX_STANDARD=14

            # cmake --build . --config Release --target install

EOF
        )
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$example_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=disabled

            meson compile -C build

            ninja -C build
            ninja -C build install
EOF
        )
        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('ssl')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('libpcap', 'openssl')
        ->withLdflags('-L' . $example_prefix . '/lib/x86_64-linux-gnu/')
        ->withPkgConfig($example_prefix . '/lib/x86_64-linux-gnu/pkgconfig')
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $openssl_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $openssl_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lssl ');
};
