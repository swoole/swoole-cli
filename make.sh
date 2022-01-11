SRC=/home/htf/soft/php-8.1.1
ROOT=$(pwd)
export CC=clang
export CXX=clang++
export LD=ld.lld
export PKG_CONFIG_PATH=/usr/libwebp/lib/pkgconfig:/usr/freetype/lib/pkgconfig:/usr/libjpeg/lib64/pkgconfig:/usr/libpng/lib/pkgconfig:/usr/giflib/lib/pkgconfig:/usr/gmp/lib/pkgconfig:/usr/imagemagick/lib/pkgconfig:/usr/curl/lib/pkgconfig:/usr/openssl/lib/pkgconfig:$PKG_CONFIG_PATH
OPTIONS="--disable-all \
--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl \
--with-curl=/usr/curl \
--with-iconv=/usr/libiconv \
--with-bz2 \
--enable-bcmath \
--enable-pcntl \
--enable-filter \
--enable-session \
--enable-tokenizer \
--enable-mbstring \
--enable-ctype \
--with-zlib \
--with-zip \
--enable-swoole --enable-sockets --enable-mysqlnd --enable-http2 --enable-swoole-json --enable-swoole-curl --enable-cares \
--enable-posix \
--enable-sockets \
--enable-pdo \
--with-sqlite3 \
--enable-phar \
--enable-mysqlnd \
--enable-mysqlnd \
--enable-intl \
--enable-fileinfo \
--with-pdo_mysql \
--with-pdo-sqlite \
--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
--with-xsl \
--with-gmp=/usr/gmp \
--enable-exif \
--enable-gd --with-jpeg=/usr/libjpeg  --with-freetype=/usr/freetype \
--enable-redis \
--with-imagick=/usr/imagemagick \
"

make_openssl() {
    cd /work/pool/lib
    echo "build openssl"
    mkdir -p /work/pool/lib/openssl && \
    tar --strip-components=1 -C /work/pool/lib/openssl -xf /work/pool/lib/openssl-1.1.1m.tar.gz  && \
    cd openssl && \
    echo  "./config -static --static no-shared --prefix=/usr/openssl"
        ./config -static --static no-shared --prefix=/usr/openssl && \
        make -j 8   && \
    make install
    cd -
}

clean_openssl() {
    cd /work/pool/lib
    echo "clean openssl"
    cd /work/pool/lib/openssl && make clean
    cd -
}

make_curl() {
    cd /work/pool/lib
    echo "build curl"
    mkdir -p /work/pool/lib/curl && \
    tar --strip-components=1 -C /work/pool/lib/curl -xf /work/pool/lib/curl-7.80.0.tar.gz  && \
    cd curl && \
    echo  "autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl"
        autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl && \
        make -j 8   && \
    make install
    cd -
}

clean_curl() {
    cd /work/pool/lib
    echo "clean curl"
    cd /work/pool/lib/curl && make clean
    cd -
}

make_libiconv() {
    cd /work/pool/lib
    echo "build libiconv"
    mkdir -p /work/pool/lib/libiconv && \
    tar --strip-components=1 -C /work/pool/lib/libiconv -xf /work/pool/lib/libiconv-1.16.tar.gz  && \
    cd libiconv && \
    echo  "./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no"
        ./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no && \
        make -j 8   && \
    make install
    cd -
}

clean_libiconv() {
    cd /work/pool/lib
    echo "clean libiconv"
    cd /work/pool/lib/libiconv && make clean
    cd -
}

make_libxml2() {
    cd /work/pool/lib
    echo "build libxml2"
    mkdir -p /work/pool/lib/libxml2 && \
    tar --strip-components=1 -C /work/pool/lib/libxml2 -xf /work/pool/lib/libxml2-v2.9.10.tar.gz  && \
    cd libxml2 && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no"
        ./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no && \
        make -j 8   && \
    make install
    cd -
}

clean_libxml2() {
    cd /work/pool/lib
    echo "clean libxml2"
    cd /work/pool/lib/libxml2 && make clean
    cd -
}

make_libxslt() {
    cd /work/pool/lib
    echo "build libxslt"
    mkdir -p /work/pool/lib/libxslt && \
    tar --strip-components=1 -C /work/pool/lib/libxslt -xf /work/pool/lib/libxslt-v1.1.34.tar.gz  && \
    cd libxslt && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no"
        ./autogen.sh && ./configure --prefix=/usr --enable-static=yes --enable-shared=no && \
        make -j 8   && \
    make install
    cd -
}

clean_libxslt() {
    cd /work/pool/lib
    echo "clean libxslt"
    cd /work/pool/lib/libxslt && make clean
    cd -
}

make_imagemagick() {
    cd /work/pool/lib
    echo "build imagemagick"
    mkdir -p /work/pool/lib/imagemagick && \
    tar --strip-components=1 -C /work/pool/lib/imagemagick -xf /work/pool/lib/7.1.0-19.tar.gz  && \
    cd imagemagick && \
    echo  "./configure --prefix=/usr/imagemagick --enable-static --disable-shared"
        ./configure --prefix=/usr/imagemagick --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_imagemagick() {
    cd /work/pool/lib
    echo "clean imagemagick"
    cd /work/pool/lib/imagemagick && make clean
    cd -
}

make_gmp() {
    cd /work/pool/lib
    echo "build gmp"
    mkdir -p /work/pool/lib/gmp && \
    tar --strip-components=1 -C /work/pool/lib/gmp -xf /work/pool/lib/gmp-6.2.1.tar.lz  && \
    cd gmp && \
    echo  "./configure --prefix=/usr/gmp --enable-static --disable-shared"
        ./configure --prefix=/usr/gmp --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_gmp() {
    cd /work/pool/lib
    echo "clean gmp"
    cd /work/pool/lib/gmp && make clean
    cd -
}

make_giflib() {
    cd /work/pool/lib
    echo "build giflib"
    mkdir -p /work/pool/lib/giflib && \
    tar --strip-components=1 -C /work/pool/lib/giflib -xf /work/pool/lib/giflib-5.2.1.tar.gz  && \
    cd giflib && \
    echo  ""
        make -j 8  libgif.a && \
    make install
    cd -
}

clean_giflib() {
    cd /work/pool/lib
    echo "clean giflib"
    cd /work/pool/lib/giflib && make clean
    cd -
}

make_libpng() {
    cd /work/pool/lib
    echo "build libpng"
    mkdir -p /work/pool/lib/libpng && \
    tar --strip-components=1 -C /work/pool/lib/libpng -xf /work/pool/lib/libpng-1.6.37.tar.gz  && \
    cd libpng && \
    echo  "./configure --prefix=/usr/libpng --enable-static --disable-shared"
        ./configure --prefix=/usr/libpng --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_libpng() {
    cd /work/pool/lib
    echo "clean libpng"
    cd /work/pool/lib/libpng && make clean
    cd -
}

make_libjpeg() {
    cd /work/pool/lib
    echo "build libjpeg"
    mkdir -p /work/pool/lib/libjpeg && \
    tar --strip-components=1 -C /work/pool/lib/libjpeg -xf /work/pool/lib/libjpeg-turbo-2.1.2.tar.gz  && \
    cd libjpeg && \
    echo  "cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/libjpeg ."
        cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/libjpeg . && \
        make -j 8   && \
    make install
    cd -
}

clean_libjpeg() {
    cd /work/pool/lib
    echo "clean libjpeg"
    cd /work/pool/lib/libjpeg && make clean
    cd -
}

make_freetype() {
    cd /work/pool/lib
    echo "build freetype"
    mkdir -p /work/pool/lib/freetype && \
    tar --strip-components=1 -C /work/pool/lib/freetype -xf /work/pool/lib/freetype-2.10.4.tar.gz  && \
    cd freetype && \
    echo  "./configure --prefix=/usr/freetype --enable-static --disable-shared"
        ./configure --prefix=/usr/freetype --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_freetype() {
    cd /work/pool/lib
    echo "clean freetype"
    cd /work/pool/lib/freetype && make clean
    cd -
}

make_libwebp() {
    cd /work/pool/lib
    echo "build libwebp"
    mkdir -p /work/pool/lib/libwebp && \
    tar --strip-components=1 -C /work/pool/lib/libwebp -xf /work/pool/lib/libwebp-1.2.1.tar.gz  && \
    cd libwebp && \
    echo  "./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared"
        ./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_libwebp() {
    cd /work/pool/lib
    echo "clean libwebp"
    cd /work/pool/lib/libwebp && make clean
    cd -
}

make_sqlite3() {
    cd /work/pool/lib
    echo "build sqlite3"
    mkdir -p /work/pool/lib/sqlite3 && \
    tar --strip-components=1 -C /work/pool/lib/sqlite3 -xf /work/pool/lib/sqlite-autoconf-3370000.tar.gz  && \
    cd sqlite3 && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
        ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_sqlite3() {
    cd /work/pool/lib
    echo "clean sqlite3"
    cd /work/pool/lib/sqlite3 && make clean
    cd -
}

make_zlib() {
    cd /work/pool/lib
    echo "build zlib"
    mkdir -p /work/pool/lib/zlib && \
    tar --strip-components=1 -C /work/pool/lib/zlib -xf /work/pool/lib/zlib-1.2.11.tar.gz  && \
    cd zlib && \
    echo  "./configure --prefix=/usr --static"
        ./configure --prefix=/usr --static && \
        make -j 8   && \
    make install
    cd -
}

clean_zlib() {
    cd /work/pool/lib
    echo "clean zlib"
    cd /work/pool/lib/zlib && make clean
    cd -
}

make_bzip2() {
    cd /work/pool/lib
    echo "build bzip2"
    mkdir -p /work/pool/lib/bzip2 && \
    tar --strip-components=1 -C /work/pool/lib/bzip2 -xf /work/pool/lib/bzip2-1.0.8.tar.gz  && \
    cd bzip2 && \
    echo  ""
        make -j 8  PREFIX=/usr/bzip2 && \
    make install
    cd -
}

clean_bzip2() {
    cd /work/pool/lib
    echo "clean bzip2"
    cd /work/pool/lib/bzip2 && make clean
    cd -
}

make_icu() {
    cd /work/pool/lib
    echo "build icu"
    mkdir -p /work/pool/lib/icu && \
    tar --strip-components=1 -C /work/pool/lib/icu -xf /work/pool/lib/icu4c-60_3-src.tgz  && \
    cd icu && \
    echo  "source/runConfigureICU Linux --prefix=/usr --enable-static --disable-shared"
        source/runConfigureICU Linux --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_icu() {
    cd /work/pool/lib
    echo "clean icu"
    cd /work/pool/lib/icu && make clean
    cd -
}

make_oniguruma() {
    cd /work/pool/lib
    echo "build oniguruma"
    mkdir -p /work/pool/lib/oniguruma && \
    tar --strip-components=1 -C /work/pool/lib/oniguruma -xf /work/pool/lib/oniguruma-6.9.7.tar.gz  && \
    cd oniguruma && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared"
        ./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_oniguruma() {
    cd /work/pool/lib
    echo "clean oniguruma"
    cd /work/pool/lib/oniguruma && make clean
    cd -
}

make_zip() {
    cd /work/pool/lib
    echo "build zip"
    mkdir -p /work/pool/lib/zip && \
    tar --strip-components=1 -C /work/pool/lib/zip -xf /work/pool/lib/libzip-1.8.0.tar.gz  && \
    cd zip && \
    echo  "cmake . -DBUILD_SHARED_LIBS=OFF -DOPENSSL_USE_STATIC_LIBS=TRUE -DCMAKE_INSTALL_PREFIX=/usr"
        cmake . -DBUILD_SHARED_LIBS=OFF -DOPENSSL_USE_STATIC_LIBS=TRUE -DCMAKE_INSTALL_PREFIX=/usr && \
        make -j 8   && \
    make install
    cd -
}

clean_zip() {
    cd /work/pool/lib
    echo "clean zip"
    cd /work/pool/lib/zip && make clean
    cd -
}

make_cares() {
    cd /work/pool/lib
    echo "build cares"
    mkdir -p /work/pool/lib/cares && \
    tar --strip-components=1 -C /work/pool/lib/cares -xf /work/pool/lib/c-ares-1.18.1.tar.gz  && \
    cd cares && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
        ./configure --prefix=/usr --enable-static --disable-shared && \
        make -j 8   && \
    make install
    cd -
}

clean_cares() {
    cd /work/pool/lib
    echo "clean cares"
    cd /work/pool/lib/cares && make clean
    cd -
}


make_all_library() {
    make_openssl && echo "[SUCCESS] make openssl"
    make_curl && echo "[SUCCESS] make curl"
    make_libiconv && echo "[SUCCESS] make libiconv"
    make_libxml2 && echo "[SUCCESS] make libxml2"
    make_libxslt && echo "[SUCCESS] make libxslt"
    make_imagemagick && echo "[SUCCESS] make imagemagick"
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
    EXTRA_LDFLAGS_PROGRAM='-all-static -fno-ident -L/usr/openssl/lib -L/usr/curl/lib -L/usr/libiconv/lib -L/usr/imagemagick/lib -L/usr/gmp/lib -L/usr/giflib/lib -L/usr/libpng/lib -L/usr/libjpeg/lib64 -L/usr/freetype/lib -L/usr/libwebp/lib -L/usr/bzip2/lib '  -j 8 && echo ""
}

help() {
    echo "php prepare.php config"
    echo "php prepare.php build"
}

if [ "$1" = "docker-build" ] ;then
  sudo docker build -t phpswoole/swoole_cli_os:latest .
elif [ "$1" = "docker-bash" ] ;then
  sudo docker run -it -v $ROOT:/work -v /home/htf/workspace/swoole:/work/ext/swoole phpswoole/swoole_cli_os /bin/bash
elif [ "$1" = "all-library" ] ;then
    make_all_library
elif [ "$1" = "openssl" ] ;then
    make_openssl && echo "[SUCCESS] make openssl"
elif [ "$1" = "curl" ] ;then
    make_curl && echo "[SUCCESS] make curl"
elif [ "$1" = "libiconv" ] ;then
    make_libiconv && echo "[SUCCESS] make libiconv"
elif [ "$1" = "libxml2" ] ;then
    make_libxml2 && echo "[SUCCESS] make libxml2"
elif [ "$1" = "libxslt" ] ;then
    make_libxslt && echo "[SUCCESS] make libxslt"
elif [ "$1" = "imagemagick" ] ;then
    make_imagemagick && echo "[SUCCESS] make imagemagick"
elif [ "$1" = "gmp" ] ;then
    make_gmp && echo "[SUCCESS] make gmp"
elif [ "$1" = "giflib" ] ;then
    make_giflib && echo "[SUCCESS] make giflib"
elif [ "$1" = "libpng" ] ;then
    make_libpng && echo "[SUCCESS] make libpng"
elif [ "$1" = "libjpeg" ] ;then
    make_libjpeg && echo "[SUCCESS] make libjpeg"
elif [ "$1" = "freetype" ] ;then
    make_freetype && echo "[SUCCESS] make freetype"
elif [ "$1" = "libwebp" ] ;then
    make_libwebp && echo "[SUCCESS] make libwebp"
elif [ "$1" = "sqlite3" ] ;then
    make_sqlite3 && echo "[SUCCESS] make sqlite3"
elif [ "$1" = "zlib" ] ;then
    make_zlib && echo "[SUCCESS] make zlib"
elif [ "$1" = "bzip2" ] ;then
    make_bzip2 && echo "[SUCCESS] make bzip2"
elif [ "$1" = "icu" ] ;then
    make_icu && echo "[SUCCESS] make icu"
elif [ "$1" = "oniguruma" ] ;then
    make_oniguruma && echo "[SUCCESS] make oniguruma"
elif [ "$1" = "zip" ] ;then
    make_zip && echo "[SUCCESS] make zip"
elif [ "$1" = "cares" ] ;then
    make_cares && echo "[SUCCESS] make cares"
elif [ "$1" = "config" ] ;then
    config_php
elif [ "$1" = "build" ] ;then
    make_php
elif [ "$1" = "clean-library" ] ;then
    clean_openssl && echo "[SUCCESS] make clean [openssl]"
    clean_curl && echo "[SUCCESS] make clean [curl]"
    clean_libiconv && echo "[SUCCESS] make clean [libiconv]"
    clean_libxml2 && echo "[SUCCESS] make clean [libxml2]"
    clean_libxslt && echo "[SUCCESS] make clean [libxslt]"
    clean_imagemagick && echo "[SUCCESS] make clean [imagemagick]"
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
elif [ "$1" = "diff-configure" ] ;then
  meld $SRC/configure.ac ./configure.ac
elif [ "$1" = "pkg-check" ] ;then
    echo "openssl"
    pkg-config --libs openssl
    echo "curl"
    pkg-config --libs libcurl
    echo "libiconv"
    pkg-config --libs libiconv
    echo "libxml2"
    pkg-config --libs libxml2
    echo "libxslt"
    pkg-config --libs libxslt
    echo "imagemagick"
    pkg-config --libs ImageMagick
    echo "gmp"
    pkg-config --libs gmp
    echo "giflib"
    pkg-config --libs giflib
    echo "libpng"
    pkg-config --libs libpng
    echo "libjpeg"
    pkg-config --libs libjpeg
    echo "freetype"
    pkg-config --libs freetype
    echo "libwebp"
    pkg-config --libs libwebp
    echo "sqlite3"
    pkg-config --libs sqlite3
    echo "zlib"
    pkg-config --libs zlib
    echo "bzip2"
    pkg-config --libs bzip2
    echo "icu"
    pkg-config --libs icu
    echo "oniguruma"
    pkg-config --libs oniguruma
    echo "zip"
    pkg-config --libs zip
    echo "cares"
    pkg-config --libs cares
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
  cp -r $SRC/main ./main
  cp -r sapi/cli sapi/cli
  cp -r ./TSRM/TSRM.h main/TSRM.h
  exit 0
else
    help
fi

