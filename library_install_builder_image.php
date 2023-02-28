<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_libtiff(Preprocessor $p)
{
    $libtiff_prefix = LIBTIFF_PREFIX;
    $lib = new Library('libtiff');
    $lib->withHomePage('http://www.simplesystems.org/libtiff/')
        ->withLicense('https://gitlab.com/libtiff/libtiff/-/blob/master/LICENSE.md', Library::LICENSE_SPEC)
        ->withUrl('http://download.osgeo.org/libtiff/tiff-4.5.0.tar.gz')

        ->withPrefix($libtiff_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libtiff_prefix)
        ->withConfigure(
            <<<'EOF'
            ./configure --help
            package_names="zlib libjpeg libturbojpeg liblzma  libzstd libwebp  libwebpdecoder  libwebpdemux  libwebpmux"
            
            CPPFLAGS=$(pkg-config  --cflags-only-I --static $package_names ) \
            LDFLAGS=$(pkg-config   --libs-only-L   --static $package_names ) \
            LIBS=$(pkg-config      --libs-only-l   --static $package_names ) \
EOF
            . PHP_EOL .
            <<<EOF
            ./configure --prefix={$libtiff_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-docs \
            --disable-tests

EOF

        )
        ->withPkgName('libtiff');

    $p->addLibrary($lib);
}

function install_libraw(Preprocessor $p)
{
    $libraw_prefix = LIBRAW_PREFIX;
    $lib = new Library('libraw');
    $lib->withHomePage('https://www.libraw.org/about')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://www.libraw.org/data/LibRaw-0.21.1.tar.gz')

        ->withPrefix($libraw_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libraw_prefix)
        ->withConfigure(
            <<<'EOF'
            ./configure --help
            # ZLIB_CFLAGS=$(pkg-config  --cflags --static zlib )
            # ZLIB_LIBS=$(pkg-config    --libs   --static zlib )

          
            package_names="zlib libjpeg libturbojpeg "
            CPPFLAGS=$(pkg-config  --cflags-only-I --static $package_names ) \
            LDFLAGS=$(pkg-config   --libs-only-L   --static $package_names ) \
            LIBS=$(pkg-config      --libs-only-l   --static $package_names ) \
            LIBS="-lstdc++" \
EOF
            . PHP_EOL .
            <<<EOF
            ./configure \
            --prefix={$libraw_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-jpeg \
            --enable-zlib
EOF
        )
        ->withPkgName('librawc  libraw_r');

    $p->addLibrary($lib);
}

function install_libde265(Preprocessor $p)
{
    $libde265_prefix = LIBDE265_PREFIX;
    $lib = new Library('libde265');
    $lib->withHomePage('https://github.com/strukturag/libde265.git')
        ->withLicense('https://github.com/strukturag/libheif/blob/master/COPYING', Library::LICENSE_GPL)
        ->withUrl('https://github.com/strukturag/libde265/archive/refs/tags/v1.0.11.tar.gz')
        ->withFile('libde265-v1.0.11.tar.gz')

        ->withPrefix($libde265_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libde265_prefix)
        ->withConfigure(
            <<<EOF
        ./autogen.sh
        ./configure --help
        ./configure --prefix={$libde265_prefix} \
        --enable-shared=no \
        --enable-static=yes 
EOF
        )
        ->withPkgName('libde265');

    $p->addLibrary($lib);
}

function install_libheif(Preprocessor $p)
{
    $libheif_prefix = LIBHEIF_PREFIX;
    $lib = new Library('libheif');
    $lib->withHomePage('https://github.com/strukturag/libheif.git')
        ->withLicense('https://github.com/strukturag/libheif/blob/master/COPYING', Library::LICENSE_GPL)
        ->withUrl('https://github.com/strukturag/libheif/releases/download/v1.15.1/libheif-1.15.1.tar.gz')

        ->withPrefix($libheif_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libheif_prefix)
        ->withConfigure(
            <<<'EOF'
            ./configure --help
            
            libde265_CFLAGS=$(pkg-config  --cflags --static libde265 ) \
            libde265_LIBS=$(pkg-config    --libs   --static libde265 ) \
            libpng_CFLAGS=$(pkg-config  --cflags --static libpng ) \
            libpng_LIBS=$(pkg-config    --libs   --static libpng ) \
EOF
            . PHP_EOL .
            <<<EOF
            ./configure \
            --prefix={$libheif_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('libheif');

    $p->addLibrary($lib);
}

function install_libjxl(Preprocessor $p)
{
    $libjxl_prefix = LIBJXL_PREFIX;
    $lib = new Library('libjxl');
    $lib->withHomePage('https://github.com/libjxl/libjxl.git')
        ->withLicense('https://github.com/libjxl/libjxl/blob/main/LICENSE', Library::LICENSE_BSD)
        ->withUrl('https://github.com/libjxl/libjxl/archive/refs/tags/v0.8.1.tar.gz')
        ->withManual('https://github.com/libjxl/libjxl/blob/main/BUILDING.md')
        ->withFile('libjpegxl-v0.8.1.tar.gz')
        ->withPrefix($libjxl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanInstallDirectory($libjxl_prefix)
        ->withConfigure(
            <<<EOF
        //下载依赖
        sh deps.sh
        mkdir build
        cd build
        cmake -DJPEGXL_STATIC=true -DCMAKE_BUILD_TYPE=Release -DBUILD_TESTING=OFF .. 
        cmake --build . -- -j$(nproc)
        exit 0 
        cmake --install . 
EOF
        )
        ->withSkipMakeAndMakeInstall()
        ->withPkgName('libjxl');

    $p->addLibrary($lib);
}