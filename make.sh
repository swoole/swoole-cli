SRC=/home/htf/soft/php-8.1.8
ROOT=$(pwd)
export CC=clang
export CXX=clang++
export LD=ld.lld
export PKG_CONFIG_PATH=/usr/libyaml/lib/pkgconfig:/usr/curl/lib/pkgconfig:/usr/imagemagick/lib/pkgconfig:/usr/libwebp/lib/pkgconfig:/usr/freetype/lib/pkgconfig:/usr/lib64/pkgconfig:/usr/libpng/lib/pkgconfig:/usr/gmp/lib/pkgconfig:/usr/openssl/lib/pkgconfig:$PKG_CONFIG_PATH
OPTIONS="--disable-all \
--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl \
--with-curl \
--with-iconv=/usr/libiconv \
--with-bz2=/usr/bzip2 \
--enable-bcmath \
--enable-pcntl \
--enable-filter \
--enable-session \
--enable-tokenizer \
--enable-mbstring \
--enable-ctype \
--with-zlib \
--with-zip \
--enable-posix \
--enable-sockets \
--enable-pdo \
--with-sqlite3 \
--enable-phar \
--enable-mysqlnd \
--with-mysqli \
--enable-intl \
--enable-fileinfo \
--with-pdo_mysql \
--with-pdo-sqlite \
--enable-soap \
--with-xsl \
--with-gmp=/usr/gmp \
--enable-exif \
--with-sodium \
--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
--enable-gd --with-jpeg=/usr --with-freetype=/usr \
--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
--enable-redis \
--with-imagick=/usr/imagemagick \
--with-yaml=/usr/libyaml \
"

make_openssl() {
    cd /work/thirdparty
    echo "build openssl"
    mkdir -p /work/thirdparty/openssl && \
    tar --strip-components=1 -C /work/thirdparty/openssl -xf /work/pool/lib/openssl-1.1.1m.tar.gz  && \
    cd openssl && \
    echo  "./config -static --static no-shared --prefix=/usr/openssl"
        ./config -static --static no-shared --prefix=/usr/openssl && \
        make -j 8   && \
    make install
    cd -
}

clean_openssl() {
    cd /work/thirdparty
    echo "clean openssl"
    cd /work/thirdparty/openssl && make clean
    cd -
}

make_libiconv() {
    cd /work/thirdparty
    echo "build libiconv"
    mkdir -p /work/thirdparty/libiconv && \
    tar --strip-components=1 -C /work/thirdparty/libiconv -xf /work/pool/lib/libiconv-1.16.tar.gz  && \
    cd libiconv && \
    echo  "./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no"
        ./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no && \
        make -j 8   && \
    make install
    cd -
}

clean_libiconv() {
    cd /work/thirdparty
    echo "clean libiconv"
    cd /work/thirdparty/libiconv && make clean
    cd -
}

make_libxml2() {
    cd /work/thirdparty
    echo "build libxml2"
    mkdir -p /work/thirdparty/libxml2 && \
    tar --strip-components=1 -C /work/thirdparty/libxml2 -xf /work/pool/lib/libxml2-v2.9.10.tar.gz  && \
    cd libxml2 && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no"
        ./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no && \
        make -j 8   && \
    make install
    cd -
}

clean_libxml2() {
    cd /work/thirdparty
    echo "clean libxml2"
    cd /work/thirdparty/libxml2 && make clean
    cd -
}

make_libxslt() {
    cd /work/thirdparty
    echo "build libxslt"
    mkdir -p /work/thirdparty/libxslt && \
    tar --strip-components=1 -C /work/thirdparty/libxslt -xf /work/pool/lib/libxslt-v1.1.34.tar.gz  && \
    cd libxslt && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no"
        ./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no && \
        make -j 8   && \
    make install
    cd -
}

clean_libxslt() {
    cd /work/thirdparty
    echo "clean libxslt"
    cd /work/thirdparty/libxslt && make clean
    cd -
}

make_gmp() {
    cd /work/thirdparty
    echo "build gmp"
    mkdir -p /work/thirdparty/gmp && \
    tar --strip-components=1 -C /work/thirdparty/gmp -xf /work/pool/lib/gmp-6.2.1.tar.lz  && \
    cd gmp && \
    echo  "./configure --prefix=/usr/gmp --enable-static --disable-shared"
        ./configure --prefix=/usr/gmp --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_gmp() {
    cd /work/thirdparty
    echo "clean gmp"
    cd /work/thirdparty/gmp && make clean
    cd -
}

make_giflib() {
    cd /work/thirdparty
    echo "build giflib"
    mkdir -p /work/thirdparty/giflib && \
    tar --strip-components=1 -C /work/thirdparty/giflib -xf /work/pool/lib/giflib-5.2.1.tar.gz  && \
    cd giflib && \
    echo  ""
        make -j 8  libgif.a && \
    make install
    cd -
}

clean_giflib() {
    cd /work/thirdparty
    echo "clean giflib"
    cd /work/thirdparty/giflib && make clean
    cd -
}

make_libpng() {
    cd /work/thirdparty
    echo "build libpng"
    mkdir -p /work/thirdparty/libpng && \
    tar --strip-components=1 -C /work/thirdparty/libpng -xf /work/pool/lib/libpng-1.6.37.tar.gz  && \
    cd libpng && \
    echo  "./configure --prefix=/usr/libpng --enable-static --disable-shared"
        ./configure --prefix=/usr/libpng --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_libpng() {
    cd /work/thirdparty
    echo "clean libpng"
    cd /work/thirdparty/libpng && make clean
    cd -
}

make_libjpeg() {
    cd /work/thirdparty
    echo "build libjpeg"
    mkdir -p /work/thirdparty/libjpeg && \
    tar --strip-components=1 -C /work/thirdparty/libjpeg -xf /work/pool/lib/libjpeg-turbo-2.1.2.tar.gz  && \
    cd libjpeg && \
    echo  "cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr ."
        cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr . && \
        make -j 8   && \
    make install
    cd -
}

clean_libjpeg() {
    cd /work/thirdparty
    echo "clean libjpeg"
    cd /work/thirdparty/libjpeg && make clean
    cd -
}

make_freetype() {
    cd /work/thirdparty
    echo "build freetype"
    mkdir -p /work/thirdparty/freetype && \
    tar --strip-components=1 -C /work/thirdparty/freetype -xf /work/pool/lib/freetype-2.10.4.tar.gz  && \
    cd freetype && \
    echo  "./configure --prefix=/usr/freetype --enable-static --disable-shared"
        ./configure --prefix=/usr/freetype --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_freetype() {
    cd /work/thirdparty
    echo "clean freetype"
    cd /work/thirdparty/freetype && make clean
    cd -
}

make_libwebp() {
    cd /work/thirdparty
    echo "build libwebp"
    mkdir -p /work/thirdparty/libwebp && \
    tar --strip-components=1 -C /work/thirdparty/libwebp -xf /work/pool/lib/libwebp-1.2.1.tar.gz  && \
    cd libwebp && \
    echo  "./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared"
        ./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_libwebp() {
    cd /work/thirdparty
    echo "clean libwebp"
    cd /work/thirdparty/libwebp && make clean
    cd -
}

make_sqlite3() {
    cd /work/thirdparty
    echo "build sqlite3"
    mkdir -p /work/thirdparty/sqlite3 && \
    tar --strip-components=1 -C /work/thirdparty/sqlite3 -xf /work/pool/lib/sqlite-autoconf-3370000.tar.gz  && \
    cd sqlite3 && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
        ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_sqlite3() {
    cd /work/thirdparty
    echo "clean sqlite3"
    cd /work/thirdparty/sqlite3 && make clean
    cd -
}

make_zlib() {
    cd /work/thirdparty
    echo "build zlib"
    mkdir -p /work/thirdparty/zlib && \
    tar --strip-components=1 -C /work/thirdparty/zlib -xf /work/pool/lib/zlib-1.2.11.tar.gz  && \
    cd zlib && \
    echo  "./configure --prefix=/usr --static"
        ./configure --prefix=/usr --static && \
        make -j 8   && \
    make install
    cd -
}

clean_zlib() {
    cd /work/thirdparty
    echo "clean zlib"
    cd /work/thirdparty/zlib && make clean
    cd -
}

make_bzip2() {
    cd /work/thirdparty
    echo "build bzip2"
    mkdir -p /work/thirdparty/bzip2 && \
    tar --strip-components=1 -C /work/thirdparty/bzip2 -xf /work/pool/lib/bzip2-1.0.8.tar.gz  && \
    cd bzip2 && \
    echo  ""
        make -j 8  PREFIX=/usr/bzip2 && \
    make install
    cd -
}

clean_bzip2() {
    cd /work/thirdparty
    echo "clean bzip2"
    cd /work/thirdparty/bzip2 && make clean
    cd -
}

make_icu() {
    cd /work/thirdparty
    echo "build icu"
    mkdir -p /work/thirdparty/icu && \
    tar --strip-components=1 -C /work/thirdparty/icu -xf /work/pool/lib/icu4c-60_3-src.tgz  && \
    cd icu && \
    echo  "source/runConfigureICU Linux --prefix=/usr --enable-static --disable-shared"
        source/runConfigureICU Linux --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_icu() {
    cd /work/thirdparty
    echo "clean icu"
    cd /work/thirdparty/icu && make clean
    cd -
}

make_oniguruma() {
    cd /work/thirdparty
    echo "build oniguruma"
    mkdir -p /work/thirdparty/oniguruma && \
    tar --strip-components=1 -C /work/thirdparty/oniguruma -xf /work/pool/lib/oniguruma-6.9.7.tar.gz  && \
    cd oniguruma && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared"
        ./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_oniguruma() {
    cd /work/thirdparty
    echo "clean oniguruma"
    cd /work/thirdparty/oniguruma && make clean
    cd -
}

make_zip() {
    cd /work/thirdparty
    echo "build zip"
    mkdir -p /work/thirdparty/zip && \
    tar --strip-components=1 -C /work/thirdparty/zip -xf /work/pool/lib/libzip-1.8.0.tar.gz  && \
    cd zip && \
    echo  "cmake . -DBUILD_SHARED_LIBS=OFF -DOPENSSL_USE_STATIC_LIBS=TRUE -DCMAKE_INSTALL_PREFIX=/usr"
        cmake . -DBUILD_SHARED_LIBS=OFF -DOPENSSL_USE_STATIC_LIBS=TRUE -DCMAKE_INSTALL_PREFIX=/usr && \
        make -j 8   && \
    make install
    cd -
}

clean_zip() {
    cd /work/thirdparty
    echo "clean zip"
    cd /work/thirdparty/zip && make clean
    cd -
}

make_cares() {
    cd /work/thirdparty
    echo "build cares"
    mkdir -p /work/thirdparty/cares && \
    tar --strip-components=1 -C /work/thirdparty/cares -xf /work/pool/lib/c-ares-1.18.1.tar.gz  && \
    cd cares && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
        ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_cares() {
    cd /work/thirdparty
    echo "clean cares"
    cd /work/thirdparty/cares && make clean
    cd -
}

make_imagemagick() {
    cd /work/thirdparty
    echo "build imagemagick"
    mkdir -p /work/thirdparty/imagemagick && \
    tar --strip-components=1 -C /work/thirdparty/imagemagick -xf /work/pool/lib/7.1.0-19.tar.gz  && \
    cd imagemagick && \
    echo  "./configure --prefix=/usr/imagemagick --with-zip=no --enable-static --disable-shared"
        ./configure --prefix=/usr/imagemagick --with-zip=no --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_imagemagick() {
    cd /work/thirdparty
    echo "clean imagemagick"
    cd /work/thirdparty/imagemagick && make clean
    cd -
}

make_curl() {
    cd /work/thirdparty
    echo "build curl"
    mkdir -p /work/thirdparty/curl && \
    tar --strip-components=1 -C /work/thirdparty/curl -xf /work/pool/lib/curl-7.80.0.tar.gz  && \
    cd curl && \
    echo  "autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl --without-librtmp --without-brotli --without-libidn2 --disable-ldap --disable-rtsp --without-zstd --without-nghttp2 --without-nghttp3"
        autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl --without-librtmp --without-brotli --without-libidn2 --disable-ldap --disable-rtsp --without-zstd --without-nghttp2 --without-nghttp3 && \
        make -j 8   && \
    make install
    cd -
}

clean_curl() {
    cd /work/thirdparty
    echo "clean curl"
    cd /work/thirdparty/curl && make clean
    cd -
}

make_libsodium() {
    cd /work/thirdparty
    echo "build libsodium"
    mkdir -p /work/thirdparty/libsodium && \
    tar --strip-components=1 -C /work/thirdparty/libsodium -xf /work/pool/lib/libsodium-1.0.18.tar.gz  && \
    cd libsodium && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
        ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_libsodium() {
    cd /work/thirdparty
    echo "clean libsodium"
    cd /work/thirdparty/libsodium && make clean
    cd -
}

make_libyaml() {
    cd /work/thirdparty
    echo "build libyaml"
    mkdir -p /work/thirdparty/libyaml && \
    tar --strip-components=1 -C /work/thirdparty/libyaml -xf /work/pool/lib/yaml-0.2.5.tar.gz  && \
    cd libyaml && \
    echo  "./configure --prefix=/usr/libyaml --enable-static --disable-shared"
        ./configure --prefix=/usr/libyaml --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_libyaml() {
    cd /work/thirdparty
    echo "clean libyaml"
    cd /work/thirdparty/libyaml && make clean
    cd -
}


make_all_library() {
    make_openssl && echo "[SUCCESS] make openssl"
    make_libiconv && echo "[SUCCESS] make libiconv"
    make_libxml2 && echo "[SUCCESS] make libxml2"
    make_libxslt && echo "[SUCCESS] make libxslt"
    make_gmp && echo "[SUCCESS] make gmp"
    make_giflib && echo "[SUCCESS] make giflib"
    make_libpng && echo "[SUCCESS] make libpng"
    make_libjpeg && echo "[SUCCESS] make libjpeg"
    make_freetype && echo "[SUCCESS] make freetype"
    make_libwebp && echo "[SUCCESS] make libwebp"
    make_sqlite3 && echo "[SUCCESS] make sqlite3"
    make_zlib && echo "[SUCCESS] make zlib"
    make_bzip2 && echo "[SUCCESS] make bzip2"
    make_icu && echo "[SUCCESS] make icu"
    make_oniguruma && echo "[SUCCESS] make oniguruma"
    make_zip && echo "[SUCCESS] make zip"
    make_cares && echo "[SUCCESS] make cares"
    make_imagemagick && echo "[SUCCESS] make imagemagick"
    make_curl && echo "[SUCCESS] make curl"
    make_libsodium && echo "[SUCCESS] make libsodium"
    make_libyaml && echo "[SUCCESS] make libyaml"
}

config_php() {
    rm ./configure
    ./buildconf --force
    mv main/php_config.h.in /tmp/cnt
    echo -ne '#ifndef __PHP_CONFIG_H\n#define __PHP_CONFIG_H\n' > main/php_config.h.in
    cat /tmp/cnt >> main/php_config.h.in
    echo -ne '\n#endif\n' >> main/php_config.h.in
    echo $OPTIONS
    echo $PKG_CONFIG_PATH
    ./configure $OPTIONS
}

make_php() {
    make EXTRA_CFLAGS='-fno-ident -Xcompiler -march=nehalem -Xcompiler -mtune=haswell -Os' \
    EXTRA_LDFLAGS_PROGRAM='-all-static -fno-ident  -L/usr/openssl/lib -L/usr/libiconv/lib -L/usr/gmp/lib -L/usr/libpng/lib -L/usr/lib64 -L/usr/freetype/lib -L/usr/libwebp/lib -L/usr/bzip2/lib -L/usr/imagemagick/lib -L/usr/curl/lib -L/usr/libyaml/lib '  -j 8 && echo ""
}

help() {
    echo "./make.sh docker-bash"
    echo "./make.sh config"
    echo "./make.sh build"
    echo "./make.sh archive"
    echo "./make.sh all-library"
    echo "./make.sh clean-all-library"
    echo "./make.sh sync"
}

if [ "$1" = "docker-build" ] ;then
  sudo docker build -t phpswoole/swoole_cli_os:1.4 .
elif [ "$1" = "docker-bash" ] ;then
    sudo docker run -it -v $ROOT:/work -v /home/htf/workspace/swoole:/work/ext/swoole phpswoole/swoole_cli_os:1.4 /bin/bash
    exit 0
elif [ "$1" = "all-library" ] ;then
    make_all_library
elif [ "$1" = "openssl" ] ;then
    make_openssl && echo "[SUCCESS] make openssl"
elif [ "$1" = "clean-openssl" ] ;then
    clean_openssl && echo "[SUCCESS] make clean openssl"
elif [ "$1" = "libiconv" ] ;then
    make_libiconv && echo "[SUCCESS] make libiconv"
elif [ "$1" = "clean-libiconv" ] ;then
    clean_libiconv && echo "[SUCCESS] make clean libiconv"
elif [ "$1" = "libxml2" ] ;then
    make_libxml2 && echo "[SUCCESS] make libxml2"
elif [ "$1" = "clean-libxml2" ] ;then
    clean_libxml2 && echo "[SUCCESS] make clean libxml2"
elif [ "$1" = "libxslt" ] ;then
    make_libxslt && echo "[SUCCESS] make libxslt"
elif [ "$1" = "clean-libxslt" ] ;then
    clean_libxslt && echo "[SUCCESS] make clean libxslt"
elif [ "$1" = "gmp" ] ;then
    make_gmp && echo "[SUCCESS] make gmp"
elif [ "$1" = "clean-gmp" ] ;then
    clean_gmp && echo "[SUCCESS] make clean gmp"
elif [ "$1" = "giflib" ] ;then
    make_giflib && echo "[SUCCESS] make giflib"
elif [ "$1" = "clean-giflib" ] ;then
    clean_giflib && echo "[SUCCESS] make clean giflib"
elif [ "$1" = "libpng" ] ;then
    make_libpng && echo "[SUCCESS] make libpng"
elif [ "$1" = "clean-libpng" ] ;then
    clean_libpng && echo "[SUCCESS] make clean libpng"
elif [ "$1" = "libjpeg" ] ;then
    make_libjpeg && echo "[SUCCESS] make libjpeg"
elif [ "$1" = "clean-libjpeg" ] ;then
    clean_libjpeg && echo "[SUCCESS] make clean libjpeg"
elif [ "$1" = "freetype" ] ;then
    make_freetype && echo "[SUCCESS] make freetype"
elif [ "$1" = "clean-freetype" ] ;then
    clean_freetype && echo "[SUCCESS] make clean freetype"
elif [ "$1" = "libwebp" ] ;then
    make_libwebp && echo "[SUCCESS] make libwebp"
elif [ "$1" = "clean-libwebp" ] ;then
    clean_libwebp && echo "[SUCCESS] make clean libwebp"
elif [ "$1" = "sqlite3" ] ;then
    make_sqlite3 && echo "[SUCCESS] make sqlite3"
elif [ "$1" = "clean-sqlite3" ] ;then
    clean_sqlite3 && echo "[SUCCESS] make clean sqlite3"
elif [ "$1" = "zlib" ] ;then
    make_zlib && echo "[SUCCESS] make zlib"
elif [ "$1" = "clean-zlib" ] ;then
    clean_zlib && echo "[SUCCESS] make clean zlib"
elif [ "$1" = "bzip2" ] ;then
    make_bzip2 && echo "[SUCCESS] make bzip2"
elif [ "$1" = "clean-bzip2" ] ;then
    clean_bzip2 && echo "[SUCCESS] make clean bzip2"
elif [ "$1" = "icu" ] ;then
    make_icu && echo "[SUCCESS] make icu"
elif [ "$1" = "clean-icu" ] ;then
    clean_icu && echo "[SUCCESS] make clean icu"
elif [ "$1" = "oniguruma" ] ;then
    make_oniguruma && echo "[SUCCESS] make oniguruma"
elif [ "$1" = "clean-oniguruma" ] ;then
    clean_oniguruma && echo "[SUCCESS] make clean oniguruma"
elif [ "$1" = "zip" ] ;then
    make_zip && echo "[SUCCESS] make zip"
elif [ "$1" = "clean-zip" ] ;then
    clean_zip && echo "[SUCCESS] make clean zip"
elif [ "$1" = "cares" ] ;then
    make_cares && echo "[SUCCESS] make cares"
elif [ "$1" = "clean-cares" ] ;then
    clean_cares && echo "[SUCCESS] make clean cares"
elif [ "$1" = "imagemagick" ] ;then
    make_imagemagick && echo "[SUCCESS] make imagemagick"
elif [ "$1" = "clean-imagemagick" ] ;then
    clean_imagemagick && echo "[SUCCESS] make clean imagemagick"
elif [ "$1" = "curl" ] ;then
    make_curl && echo "[SUCCESS] make curl"
elif [ "$1" = "clean-curl" ] ;then
    clean_curl && echo "[SUCCESS] make clean curl"
elif [ "$1" = "libsodium" ] ;then
    make_libsodium && echo "[SUCCESS] make libsodium"
elif [ "$1" = "clean-libsodium" ] ;then
    clean_libsodium && echo "[SUCCESS] make clean libsodium"
elif [ "$1" = "libyaml" ] ;then
    make_libyaml && echo "[SUCCESS] make libyaml"
elif [ "$1" = "clean-libyaml" ] ;then
    clean_libyaml && echo "[SUCCESS] make clean libyaml"
elif [ "$1" = "config" ] ;then
    config_php
elif [ "$1" = "build" ] ;then
    make_php
elif [ "$1" = "archive" ] ;then
    cd bin
    SWOOLE_VERSION=$(./swoole-cli -r "echo SWOOLE_VERSION;")
    SWOOLE_CLI_FILE=swoole-cli-v${SWOOLE_VERSION}-linux-x64.tar.xz
    strip swoole-cli
    tar -cJvf ${SWOOLE_CLI_FILE} swoole-cli LICENSE
    mv ${SWOOLE_CLI_FILE} ../
    cd -
elif [ "$1" = "clean-all-library" ] ;then
    clean_openssl && echo "[SUCCESS] make clean [openssl]"
    clean_libiconv && echo "[SUCCESS] make clean [libiconv]"
    clean_libxml2 && echo "[SUCCESS] make clean [libxml2]"
    clean_libxslt && echo "[SUCCESS] make clean [libxslt]"
    clean_gmp && echo "[SUCCESS] make clean [gmp]"
    clean_giflib && echo "[SUCCESS] make clean [giflib]"
    clean_libpng && echo "[SUCCESS] make clean [libpng]"
    clean_libjpeg && echo "[SUCCESS] make clean [libjpeg]"
    clean_freetype && echo "[SUCCESS] make clean [freetype]"
    clean_libwebp && echo "[SUCCESS] make clean [libwebp]"
    clean_sqlite3 && echo "[SUCCESS] make clean [sqlite3]"
    clean_zlib && echo "[SUCCESS] make clean [zlib]"
    clean_bzip2 && echo "[SUCCESS] make clean [bzip2]"
    clean_icu && echo "[SUCCESS] make clean [icu]"
    clean_oniguruma && echo "[SUCCESS] make clean [oniguruma]"
    clean_zip && echo "[SUCCESS] make clean [zip]"
    clean_cares && echo "[SUCCESS] make clean [cares]"
    clean_imagemagick && echo "[SUCCESS] make clean [imagemagick]"
    clean_curl && echo "[SUCCESS] make clean [curl]"
    clean_libsodium && echo "[SUCCESS] make clean [libsodium]"
    clean_libyaml && echo "[SUCCESS] make clean [libyaml]"
elif [ "$1" = "diff-configure" ] ;then
  meld $SRC/configure.ac ./configure.ac
elif [ "$1" = "pkg-check" ] ;then
    echo "[openssl]"
    pkg-config --libs openssl
    echo "==========================================================="
    echo "[libiconv]"
    pkg-config --libs libiconv
    echo "==========================================================="
    echo "[libxml2]"
    pkg-config --libs libxml-2.0
    echo "==========================================================="
    echo "[libxslt]"
    pkg-config --libs libxslt
    echo "==========================================================="
    echo "[gmp]"
    pkg-config --libs gmp
    echo "==========================================================="
    echo "[giflib]"
    pkg-config --libs giflib
    echo "==========================================================="
    echo "[libpng]"
    pkg-config --libs libpng
    echo "==========================================================="
    echo "[libjpeg]"
    pkg-config --libs libjpeg
    echo "==========================================================="
    echo "[freetype]"
    pkg-config --libs freetyp2
    echo "==========================================================="
    echo "[libwebp]"
    pkg-config --libs libwebp
    echo "==========================================================="
    echo "[sqlite3]"
    pkg-config --libs sqlite3
    echo "==========================================================="
    echo "[zlib]"
    pkg-config --libs zlib
    echo "==========================================================="
    echo "[bzip2]"
    pkg-config --libs bzip2
    echo "==========================================================="
    echo "[icu]"
    pkg-config --libs icu-i18n
    echo "==========================================================="
    echo "[oniguruma]"
    pkg-config --libs oniguruma
    echo "==========================================================="
    echo "[zip]"
    pkg-config --libs libzip
    echo "==========================================================="
    echo "[cares]"
    pkg-config --libs libcares
    echo "==========================================================="
    echo "[imagemagick]"
    pkg-config --libs ImageMagick
    echo "==========================================================="
    echo "[curl]"
    pkg-config --libs libcurl
    echo "==========================================================="
    echo "[libsodium]"
    pkg-config --libs libsodium
    echo "==========================================================="
    echo "[libyaml]"
    pkg-config --libs yaml-0.1
    echo "==========================================================="
elif [ "$1" = "sync" ] ;then
  echo "sync"
  # ZendVM
  cp -r $SRC/Zend ./
  # Extension
  cp -r $SRC/ext/bcmath/ ./ext
  cp -r $SRC/ext/bz2/ ./ext
  cp -r $SRC/ext/calendar/ ./ext
  cp -r $SRC/ext/ctype/ ./ext
  cp -r $SRC/ext/curl/ ./ext
  cp -r $SRC/ext/date/ ./ext
  cp -r $SRC/ext/dom/ ./ext
  cp -r $SRC/ext/exif/ ./ext
  cp -r $SRC/ext/fileinfo/ ./ext
  cp -r $SRC/ext/filter/ ./ext
  cp -r $SRC/ext/gd/ ./ext
  cp -r $SRC/ext/gettext/ ./ext
  cp -r $SRC/ext/gmp/ ./ext
  cp -r $SRC/ext/hash/ ./ext
  cp -r $SRC/ext/iconv/ ./ext
  cp -r $SRC/ext/intl/ ./ext
  cp -r $SRC/ext/json/ ./ext
  cp -r $SRC/ext/libxml/ ./ext
  cp -r $SRC/ext/mbstring/ ./ext
  cp -r $SRC/ext/mysqli/ ./ext
  cp -r $SRC/ext/mysqlnd/ ./ext
  cp -r $SRC/ext/opcache/ ./ext
  cp -r $SRC/ext/openssl/ ./ext
  cp -r $SRC/ext/pcntl/ ./ext
  cp -r $SRC/ext/pcre/ ./ext
  cp -r $SRC/ext/pdo/ ./ext
  cp -r $SRC/ext/pdo_mysql/ ./ext
  cp -r $SRC/ext/pdo_sqlite/ ./ext
  cp -r $SRC/ext/phar/ ./ext
  cp -r $SRC/ext/posix/ ./ext
  cp -r $SRC/ext/readline/ ./ext
  cp -r $SRC/ext/reflection/ ./ext
  cp -r $SRC/ext/session/ ./ext
  cp -r $SRC/ext/simplexml/ ./ext
  cp -r $SRC/ext/soap/ ./ext
  cp -r $SRC/ext/sockets/ ./ext
  cp -r $SRC/ext/sodium/ ./ext
  cp -r $SRC/ext/spl/ ./ext
  cp -r $SRC/ext/sqlite3/ ./ext
  cp -r $SRC/ext/standard/ ./ext
  cp -r $SRC/ext/sysvshm/ ./ext
  cp -r $SRC/ext/tokenizer/ ./ext
  cp -r $SRC/ext/xml/ ./ext
  cp -r $SRC/ext/xmlreader/ ./ext
  cp -r $SRC/ext/xmlwriter/ ./ext
  cp -r $SRC/ext/xsl/ ./ext
  cp -r $SRC/ext/zip/ ./ext
  cp -r $SRC/ext/zlib/ ./ext
  # main
  cp -r $SRC/main ./
  cp -r $SRC/build ./
  cp -r ./TSRM/TSRM.h main/TSRM.h
  cp -r $SRC/configure.ac ./
  exit 0
else
    help
fi

